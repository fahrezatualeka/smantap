@extends('layout/template')

@section('content')

<style>
    .table-responsive {
        max-height: 1050px;
        overflow-y: auto;
        }
</style>
<section class="content-header">
    <h1>
        <b>Dashboard</b>
    </h1>
</section>

<section class="content">
<div class="box">
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Login!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
    @endif

        <!-- Informasi Utama Pajak -->
        {{-- <div class="col-lg-12 col-md-12">
            <div class="box"> --}}
                <div class="box-header with-border">
                    <h3 class="box-title">Statistik Sistem Aplikasi Penagihan Piutang Pajak</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/data_wajibpajak" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>                                        
                                            Data Wajib Pajak
                                    </b></span>
                                    <p>{{ $datawajibpajak }} Data</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4">
                        <a href="/data_penetapan" style="text-decoration: none; color:black;">
                        <div class="info-box" style="width: 50px height: 100px">
                            <span class="info-box-icon bg-white"><i class="fa fa-money-bill"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>Data Penetapan</b></span>
                                <p>{{ $datapenetapan }} Data</p>
                            </div>
                        </div>
                    </a>
                </div>
                    <div class="col-lg-4">
                        <a href="/data_penetapan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Jumlah Penetapan Tahun Ini</b></span>
                                    <p>Rp{{ number_format((float) $jumlah_penetapan_tahun, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                        <div class="col-lg-4">
                            <a href="/data_penetapan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah Penetapan bulan ini</b></span>
                                    <p>Belum Terhitung</p>
                                </div>
                            </div>
                        </a>
                    </div>

                        <div class="col-lg-4">
                            <a href="/data_piutang" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill-1"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Data Piutang</b></span>
                                    <p>{{ $datapiutang }} Data</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4">
                        <a href="/data_piutang" style="text-decoration: none; color:black;">
                        <div class="info-box" style="width: 50px height: 100px">
                            <span class="info-box-icon bg-white"><i class="fa fa-money-bill-1"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>Jumlah Piutang tahun ini</b></span>
                                <p>Rp{{ number_format((float) $jumlah_piutang_tahun, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                </div>

                        <div class="col-lg-4">
                            <a href="/data_piutang" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill-1"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b> Jumlah Piutang bulan ini</b></span>
                                    <p>Belum Terhitung</p>
                                </div>
                            </div>
                        </a>
                    </div>

                        <div class="col-lg-4">
                            <a href="/laporan_pelunasan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Laporan Pelunasan</b></span>
                                    <p>{{ $laporanpelunasan }} Data</p>
                                </div>
                            </div>
                        </a>
                    </div>
                        {{-- <div class="col-lg-4">
                            <a href="/laporan_pelunasan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah Pelunasan tahun ini</b></span>
                                    <p>Rp{{ number_format((float) $jumlah_pelunasan_tahun, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}
                        <div class="col-lg-4">
                            <a href="/laporan_pelunasan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah Pelunasan bulan ini</b></span>
                                    <p>Belum Terhitung</p>
                                </div>
                            </div>
                        </a>
                    </div>

                   
                        
                        <div class="col-lg-4">
                            <a href="/jenis_pajak" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-database"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Jenis Pajak</b></span>
                                    <p>{{ $jenispajak }} Jenis Pajak</p>
                                </div>
                            </div>
                        </a>
                    </div>
                        <div class="col-lg-4">
                            <a href="/kategori_pajak" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-database"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Kategori Pajak</b></span>
                                    <p>{{ $kategoripajak }} Kategori Pajak</p>
                                </div>
                            </div>
                        </a>
                    </div>
                        
                        <div class="col-lg-4">
                            <a href="/data_user" style="text-decoration: none; color:black;">
                            <div class="info-box">
                                <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Jumlah Admin</b></span>
                                    <p>{{ $admin }} Orang Admin</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-lg-4">
                            <a href="/data_user" style="text-decoration: none; color:black;">
                                <div class="info-box">
                                    <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><b>Jumlah Petugas Penagihan</b></span>
                                        {{-- <p>Zonasi 1: {{ $p1 }} Orang</p>
                                        <p>Zonasi 2: {{ $p2 }} Orang</p>
                                        <p>Zonasi 3: {{ $p3 }} Orang</p>
                                        <p>Zonasi 4: {{ $p4 }} Orang</p> --}}
                                        <p>{{ $petugaspenagihan }} Orang Petugas Penagihan</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4">
                            <a href="/data_user" style="text-decoration: none; color:black;">
                            <div class="info-box">
                                <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Jumlah Pimpinan</b></span>
                                    <p>{{ $pimpinan }} Orang Pimpinan</p>
                                </div>
                            </div>
                            </a>
                        </div>
                        

                        




                    </div>
                </div>
            {{-- </div>
        </div> --}}
</div>
</section>
@endsection