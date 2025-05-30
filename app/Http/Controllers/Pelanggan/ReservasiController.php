<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Mail\SendInvoices;
use App\Models\Jadwal;
use App\Models\Kategori_layanan;
use App\Models\Layanan;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservasiController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        $Barberman = User::where('role', 'barberman')->get();
        $Kategori = Kategori_layanan::all();
        $Layanan = Layanan::with('kategori')->get();
        $Jadwal = Jadwal::with('barberman')->get();
        return view('pelanggan.reservasi.index', compact('Barberman', 'Kategori', 'Layanan', 'Jadwal'));
    }

    public function getLayananByKategori(Request $request)
    {
        $layanan = Layanan::where('kategori_id', $request->kategori_id)->get();
        return response()->json($layanan);
    }

    public function getBarberman()
    {
        $barberman = User::where('role', 'barberman')
            ->select('id', 'name', 'foto', 'no_telepon')
            ->get();
        return response()->json($barberman);
    }

    public function getBarbermanSchedule(Request $request, $barbermanId)
    {
        $jadwal = Jadwal::where('id_barberman', $barbermanId)
            ->where('tanggal', $request->tanggal)
            ->get(['jam_mulai', 'jam_selesai']);

        // Get all possible time slots (09:00 - 21:00) with 30-minute intervals
        $allTimeSlots = [];
        for ($hour = 9; $hour < 21; $hour++) {
            foreach ([0, 30] as $minute) {
                $timeSlot = sprintf("%02d:%02d", $hour, $minute);
                $allTimeSlots[$timeSlot] = true;
            }
        }

        // Mark booked slots as unavailable
        foreach ($jadwal as $j) {
            $start = Carbon::parse($j->jam_mulai)->format('H:i');
            $allTimeSlots[$start] = false;
        }

        // Convert to array format for response
        $availableSlots = [];
        foreach ($allTimeSlots as $time => $available) {
            $availableSlots[] = [
                'time' => $time,
                'available' => $available
            ];
        }

        return response()->json($availableSlots);
    }

    public function checkout(Request $request)
    {
        try {
            if (empty(Config::$serverKey) || empty(Config::$clientKey)) {
                throw new \Exception('Midtrans configuration keys are not set.');
            }

            $orderId = 'TRX' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => Layanan::find($request->id_layanan)->harga,
                ],
                'customer_details' => [
                    'name'       => $request->name,
                    'email'      => $request->email,
                    'phone'      => $request->phone,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            // dd([
            //     'params' => $params,
            //     'request' => $request->all(),
            // ]);
            $jadwal = Jadwal::create([
                'id_barberman' => $request->id_barberman,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->id_jadwal, // The time string "13:00"
                'jam_selesai' => Carbon::parse($request->id_jadwal)->addMinutes(30)->format('H:i') // Add 30 minutes to end time
            ]);
            // Create payment record
            $pembayaran = Pembayaran::create([
                'transaksi_id'      => $params['transaction_details']['order_id'],
                'status'            => 'pending',
                'jumlah'            => Layanan::find($request->id_layanan)->harga,
                'metode_pembayaran' => '-',
                'tanggal_pembayaran' => now()
            ]);

            // Then create the reservation with the jadwal ID
            $reservasi = Reservasi::create([
                'kategori_id' => Layanan::find($request->id_layanan)->kategori_id,
                'id_layanan' => $request->id_layanan,
                'id_barberman' => $request->id_barberman,
                'id_user' => $request->id_user,
                'id_jadwal' => $jadwal->id, // Use the actual jadwal ID
                'id_pembayaran' => $pembayaran->id,
                'tanggal_reservasi' => $request->tanggal,
                'status' => 'pending'
            ]);


            // Link payment to reservation
            $reservasi->update(['id_pembayaran' => $pembayaran->id]);
            $reservasi = Reservasi::where('id', $reservasi->id)->with('kategori_layanan', 'layanan', 'barberman', 'user', 'jadwal', 'pembayaran')->first();

            return response()->json(['snapToken' => $snapToken, 'OrderId' => $orderId]);
        } catch (\Exception $e) {
            Log::error('Error during checkout: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }




    public function handlePaymentNotification(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $transactionStatus = $request->query('transaction_status');

            if (!$orderId || !$transactionStatus) {
                throw new \Exception('Invalid notification data received');
            }

            Log::info('Payment notification received', [
                'order_id' => $orderId,
                'status' => $transactionStatus
            ]);

            // Find pembayaran by transaction ID
            $pembayaran = Pembayaran::where('transaksi_id', $orderId)->first();

            if (!$pembayaran) {
                throw new \Exception("Payment with order ID {$orderId} not found");
            }

            // Update payment status
            switch ($transactionStatus) {
                case 'settlement':
                    $pembayaran->status = 'completed';
                    $pembayaran->metode_pembayaran = $request->query('payment_type');
                    break;
                case 'pending':
                    $pembayaran->status = 'pending';
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    // hapus jadwal, pembayaran, dan reservasi
                    $reservasi = Reservasi::where('id_pembayaran', $pembayaran->id)->first();
                    if ($reservasi) {
                        $reservasi->delete();
                    }
                    $jadwal = Jadwal::where('id', $reservasi->id_jadwal)->first();
                    if ($jadwal) {
                        $jadwal->delete();
                    }
                    $pembayaran->delete();
                    break;
            }

            $pembayaran->save();

            // Update related reservation status if needed
            if ($pembayaran->status == 'completed') {
                $pembayaran->reservasi()->update(['status' => 'confirmed']);
                Mail::to($pembayaran->reservasi->user->email)->send(new SendInvoices($pembayaran->reservasi));
            }

            return redirect()->route('pelanggan.riwayat')->with('success', 'Reservasi berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Payment notification error: ' . $e->getMessage());
            return redirect()->route('pelanggan.riwayat')->with('error', 'Terjadi kesalahan saat memproses pembayaran');
        }
    }
}
