<!-- header -->
<div class="top-header-area" id="sticker">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-sm-12 text-center">
                <div class="main-menu-wrap">
                    <!-- logo -->
                    <div class="site-logo">
                        <a href="index.html">
                            <img src="/assets/img/logo.png" alt="">
                        </a>
                    </div>
                    <!-- logo -->

                    <!-- menu start -->
                    <nav class="main-menu">
                        <ul>
                            <li class="{{ request()->is('/') ? 'active' : '' }}">
                                <a href="{{ url('/') }}">Home</a></li>

                            <li class="{{ request()->is('about') ? 'active' : '' }}">
                                <a href="{{ url('/about') }}">About</a></li>

                            <li class="{{ request()->is('layanan') ? 'active' : '' }}">
                                <a href="{{ url('/layanan') }}">Layanan</a></li>
                            <li class="{{ request()->is('reservasi') ? 'active' : '' }}">
                                <a href="{{ route('pelanggan.reservasi') }}">Reservasi</a></li>

                            <li class="{{ request()->is('contact') ? 'active' : '' }}"><a
                                    href="{{ url('/kontak') }}">Contact</a></li>


                            <li class="menu-item-has-children">
                                <a href="#">{{ Auth::user()->name }}</a>
                                <ul class="sub-menu">
                                    <li><a href="{{ route('pelanggan.profile') }}">Profile</a></li>
                                    <li><a href="{{ route('pelanggan.riwayat') }}">Riwayat Reservasi</a></li>
                                    <li><a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            Logout
                                        </a></li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </ul>
                            </li>

                        </ul>
                    </nav>

                    <div class="mobile-menu"></div>
                    <!-- menu end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end header -->



<style>
    .main-menu ul li.active>a {
        color: #F28123;
        /* Change this to the desired color */
    }
</style>
