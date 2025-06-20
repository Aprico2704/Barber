<x-layout>

    <!-- hero area -->
    <div class="hero-area hero-bg" style="height: 100vh;">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center">
                <div class="col-lg-9 text-center">
                    <div class="hero-text">
                        <div class="hero-text-tablecell">
                            <h1>Selamat Datang Di </h1>
                            <p class="subtitle">Barbershop Nur</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end hero area -->

    <!-- advertisement section -->
    <div class="abt-section mt-100 mb-200">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="abt-bg">
                        <img src="/assets/img/logo2.png" alt="">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="abt-text">
                        <h2>Barbershop Nur</h2>
                        <p>Barbershop Nur adalah sebuah usaha jasa potong rambut yang didirikan sejak tahun 2003 oleh Pak Nur.
                            Berlokasi di daerah Babelan, Bekasi Utara, barbershop ini telah menjadi salah satu pilihan utama masyarakat 
                            sekitar dalam memenuhi kebutuhan perawatan rambut, baik bagi pria maupun wanita..</p>
                        <a href="{{ url('/about') }}" class="boxed-btn mt-4">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end advertisement section -->

    <!-- product section -->
    <div class="product-section mt-100 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="section-title">
                        <h3><span class="orange-text">Kategori</span> Layanan</h3>
                        <p>Banyak kategori layanan yang kami siap kan untuk anda</p>
                    </div>
                </div>
            </div>

            @php $kategori = \App\Models\Kategori_layanan::with('layanan')->get(); @endphp
            <div class="row">
                @foreach ($kategori as $item)
                    <div class="col-lg-4 col-md-6 text-center">
                        <div class="single-product-item" style="height: 100%;">
                            <div class="product-image" style="height: 400px; overflow: hidden;">
                                <a href="#"><img src="{{ asset('public/storage/' . $item->gambar) }}"
                                        alt="{{ $item->nama }}"
                                        style="width: 100%; height: 100%; object-fit: cover;"></a>
                            </div>
                            <h3>{{ $item->nama }}</h3>
                            <p class="product-price">{{ $item->deskripsi }}</p>
                            <a href="{{ url('/layanan') }}" class="boxed-btn mt-3">Lihat Detail</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- end product section -->

    <!-- layanan section -->
    <div class="layanan-section mt-100 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="section-title">
                        <h3><span class="orange-text">Layanan</span> Kami</h3>
                        <p>Banyak layanan yang kami siapkan untuk anda</p>
                    </div>
                </div>
            </div>

            @php $layanan = \App\Models\Layanan::take(3)->get(); @endphp
            <div class="row">
                @foreach ($layanan as $item)
                    <div class="col-lg-4 col-md-6 text-center">
                        <div class="single-product-item" style="height: 100%;">
                            <div class="product-image" style="height: 200px; overflow: hidden;">
                                <a href="#"><img src="{{ asset('public/storage/' . $item->gambar) }}"
                                        alt="{{ $item->nama }}"
                                        style="width: 100%; height: 100%; object-fit: cover;"></a>
                            </div>
                            <h3>{{ $item->nama }}</h3>
                            <p class="product-price">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                            <a href="{{ url('/layanan') }}" class="boxed-btn mt-3">Lihat Detail</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- end layanan section -->


</x-layout>
