<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function loginAll(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        // Menghapus pengecekan verifikasi email
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $redirectUrl = match ($user->role) {
                'admin' => url('/admin/dashboard'),
                'barberman' => url('/barberman/dashboard'),
                'pelanggan' => url('/'),
                default => url('/'),
            };

            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Email atau Password Salah",
            ], 401);
        }
    }

    public function registerPelanggan(Request $request)
    {
        // Validasi data registrasi pelanggan
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'no_telepon' => 'required|numeric',
            'alamat' => 'required',
        ]);

        // Enkripsi password dan set role
        $validatedData['password'] = bcrypt($validatedData['password']);
        $validatedData['role'] = 'pelanggan';
        $validatedData['verified'] = 1;  // Langsung diset menjadi terverifikasi

        // Buat user baru
        $user = User::create($validatedData);

        // Langsung login setelah registrasi berhasil
        Auth::login($user);

        // Kembalikan response sukses
        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil! Anda sudah terdaftar.',
            'redirect' => url('/login')
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
