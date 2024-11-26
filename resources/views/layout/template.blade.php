<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SMANTAP | Pemerintah Kota Ambon</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="icon" type="image/x-icon" href="{{ asset('uploads/logo.png') }}" />

    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}

        <!-- Tambahkan jQuery dan jQuery UI -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <!-- Bootstrap CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}


{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    {{-- Notif Mengembang --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<bod class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">
        <header class="main-header">
            <a href="{{ 
                auth()->user()->role == 'admin' 
                ? route('admin.index') 
                : (auth()->user()->role == 'petugas_penagihan' 
                    ? route('petugas_penagihan.index') 
                    : route('pimpinan.index')) 
            }}" class="logo">
                <span class="logo-mini"><b>STP</b></span>
                <span class="logo-lg"> <b> SMANTAP </b></span>
            </a>
            
            
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        {{-- Kalender --}}
                        <li class="nav-item">
                            <a class="nav-link">
                                <i class="fa-solid fa-calendar-days"></i> 
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </a>
                        </li>
                        

                        {{-- Notif --}}
                        {{-- <li class="nav-item dropdown">
                            <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                <i class="fa-solid fa-bell"></i> Notifikasi
                                <span class="badge badge-danger">{{ Auth::user()->unreadNotifications->count() }}</span>
                            </a>
                            <div class="dropdown-menu">
                                @forelse (Auth::user()->unreadNotifications as $notification)
                                    <a class="dropdown-item" href="#">
                                        {{ $notification->data['message'] }}
                                    </a>
                                @empty
                                    <span class="dropdown-item">Tidak ada notifikasi baru.</span>
                                @endforelse
                            </div>
                        </li> --}}
                        
                        

                        {{-- Profil --}}
<!-- Profil -->
<!-- Profil -->
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa-solid fa-user"></i> 
        {{ Auth::check() ? Auth::user()->nama : 'Pengguna' }}
    </a>
    <ul class="dropdown-menu">
        <!-- Menu Petugas -->
        <li class="user-body">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <a href="{{ url('/profil') }}">
                        <i class="fa-solid fa-user"></i> 
                        Lihat Profil Akun
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <a href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                     <i class="fa-solid fa-right-from-bracket"></i>
                     <span>Keluar</span>
                 </a>
                 <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                     @csrf
                 </form>                 
                </div>
            </div>            
        </li>
    </ul>
</li>

                    </ul>
                </div>
            </nav>
        </header>
<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('uploads/logo.png') }}" class="light-logo" alt="logo" width="50%">
            </div>
            <div class="pull-left info">
                <p align="left">Sistem Aplikasi<br>Penagihan Piutang Pajak</p>
            </div>
        </div>
                @if (auth()->check())
                @switch(auth()->user()->role)
                    @case('admin')
                        <ul class="sidebar-menu" data-widget="tree">
                            <li class="header">PENAGIHAN</li>
                            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                                <a href="{{ url('dashboard') }}">
                                    <i class="fa fa-house"></i> <span>Dashboard</span>
                                </a>
                            </li>
                            {{-- <li class="{{ request()->is('data_piutang') ? 'active' : '' }}">
                                <a href="{{ url('data_piutang') }}">
                                    <i class="fa fa-money-bill"></i>
                                    <span>Data Piutang</span>
                                </a>
                            </li> --}}
                            <li class="{{ request()->is('data_wajibpajak') ? 'active' : '' }}">
                                <a href="{{ url('data_wajibpajak') }}">
                                    <i class="fa fa-users"></i>
                                    <span>Data Wajib Pajak</span>
                                </a>
                            </li>
        
                            <li class="{{ request()->is('data_penetapan') ? 'active' : '' }}">
                                <a href="{{ url('data_penetapan') }}">
                                    <i class="fa fa-money-bill"></i>
                                    <span>Data Penetapan</span>
                                </a>
                            </li>
                            <li class="{{ request()->is('data_piutang') ? 'active' : '' }}">
                                <a href="{{ url('data_piutang') }}">
                                    <i class="fa fa-money-bill-1"></i>
                                    <span>Data Piutang</span>
                                </a>
                            </li>
        
                            {{-- <li class="{{ request()->is('data_pelunasan') ? 'active' : '' }}">
                                <a href="{{ url('data_pelunasan') }}">
                                    <i class="fa fa-check"></i>
                                    <span>Data Pelunasan</span>
                                </a>
                            </li> --}}
                            {{-- <li class="{{ request()->is('laporan_penagihan') ? 'active' : '' }}">
                                <a href="{{ url('laporan_penagihan') }}">
                                    <i class="fa fa-file-invoice-dollar"></i>
                                    <span>Laporan Penagihan</span>
                                </a>
                            </li> --}}
                            <li class="treeview {{ in_array(request()->segment(1), ['laporan_piutang', 'laporan_pelunasan']) ? 'menu-open active' : '' }}">
                                <a href="#">
                                    <i class="fa fa-file-invoice-dollar"></i> <span>Laporan</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="{{ in_array(request()->segment(1), ['laporan_piutang', 'laporan_pelunasan']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->segment(1) == 'laporan_piutang' ? 'active' : '' }}">
                                        <a href="{{ url('laporan_piutang') }}"><i class="fa fa-dot-circle-o"></i>Piutang</a>
                                    </li>
                                    <li class="{{ request()->segment(1) == 'laporan_pelunasan' ? 'active' : '' }}">
                                        <a href="{{ url('laporan_pelunasan') }}"><i class="fa fa-dot-circle-o"></i>Pelunasan</a>
                                    </li>
                                </ul>
                                
                            </li>
                            <li class="header">PENGATURAN</li>
                            <li class="treeview {{ in_array(request()->segment(1), ['jenis_pajak', 'kategori_pajak']) ? 'menu-open active' : '' }}">
                                <a href="#">
                                    <i class="fa fa-coins"></i> <span>Pajak</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="{{ in_array(request()->segment(1), ['jenis_pajak', 'kategori_pajak']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->segment(1) == 'jenis_pajak' ? 'active' : '' }}">
                                        <a href="{{ url('jenis_pajak') }}"><i class="fa fa-dot-circle-o"></i>Jenis</a>
                                    </li>
                                    <li class="{{ request()->segment(1) == 'kategori_pajak' ? 'active' : '' }}">
                                        <a href="{{ url('kategori_pajak') }}"><i class="fa fa-dot-circle-o"></i>Kategori</a>
                                    </li>
                                </ul>
                                
                            </li>
                            <li class="{{ request()->is('data_user') ? 'active' : '' }}">
                                <a href="{{ url('data_user') }}">
                                    <i class="fa fa-user"></i> <span>Data User</span>
                                </a>
                            </li>
                            
                    @break

                    {{-- PETUGAS PENAGIHAN --}}
                    @case('petugas_penagihan')
                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="{{ request()->is('data_penagihan') ? 'active' : '' }}">
                            <a href="{{ url('data_penagihan') }}">
                                <i class="fa fa-file"></i> <span>Data Penagihan</span>
                            </a>
                        </li>
                    </ul>
                @break

                    {{-- PIMPINAN --}}
                    @case('pimpinan')
                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="{{ request()->is('dashboard_pimpinan') ? 'active' : '' }}">
                            <a href="{{ url('dashboard_pimpinan') }}">
                                <i class="fa fa-house"></i> <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                    @break
                    
                    @default
                    <p>Role tidak dikenal.</p>
            @endswitch
            @endif
        </section>
    </aside>
    
</ul>

        <div class="content-wrapper">
            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Versi</b> 2.0
                {{-- <b>Pemerintah Kota Ambon</b> --}}
            </div>
        <strong><a href="https://alakasemesta.com" target="blank">
            <img src="{{ asset('uploads/alakabizgrow.png') }}" class="img-fluid" alt="logo" width="15px" height="15px"> PT Alaka Bizgrow Inovasi</a> 2024
        </strong>
        </footer>
    </div>

    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>

    <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



    <script>
        $(document).ready(function() {
            $('#table1').DataTable()
        })
    </script>
</body>
</html>