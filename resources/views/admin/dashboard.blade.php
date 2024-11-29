@extends('layout/template')

@section('content')

<style>
    .table-responsive {
        max-height: 150px;
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
                <div class="box-body ">
                    <div class="row">
                        <div class="col-lg-4">
                            <a href="/data_wajibpajak" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>                                        
                                            Total Wajib Pajak
                                    </b></span>
                                    <p>{{ $datawajibpajak }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4">
                        <a href="/jenis_pajak" style="text-decoration: none; color:black;">
                        <div class="info-box" style="width: 50px height: 100px">
                            <span class="info-box-icon bg-white"><i class="fa fa-database"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>total Jenis Pajak</b></span>
                                <p>{{ $jenispajak }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                    <div class="col-lg-4">
                        <a href="/kategori_pajak" style="text-decoration: none; color:black;">
                        <div class="info-box" style="width: 50px height: 100px">
                            <span class="info-box-icon bg-white"><i class="fa fa-database"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>total Kategori Pajak</b></span>
                                <p>{{ $kategoripajak }}</p>
                            </div>
                        </div>
                    </a>
                </div>

                    {{-- <div class="col-lg-4">
                        <a href="/data_penetapan" style="text-decoration: none; color:black;">
                        <div class="info-box" style="width: 50px height: 100px">
                            <span class="info-box-icon bg-white"><i class="fa fa-money-bill"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>Total Tagihan Pajak</b></span>
                                <p>{{ $datapenetapan }} Data</p>
                            </div>
                        </div>
                    </a>
                </div> --}}
                    <div class="col-lg-4">
                        <a href="/data_penetapan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah Penetapan Pajak</b></span>
                                    <p>Rp{{ number_format((float) $jumlah_penetapan_tahun, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                        {{-- <div class="col-lg-4">
                            <a href="/data_penetapan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah Penetapan bulan ini</b></span>
                                    <p>Belum Terhitung</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}

                        {{-- <div class="col-lg-4">
                            <a href="/data_piutang" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill-1"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Data Piutang</b></span>
                                    <p>{{ $datapiutang }} Data</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}

                    <div class="col-lg-4">
                        <a href="/data_piutang" style="text-decoration: none; color:black;">
                        <div class="info-box" style="width: 50px height: 100px">
                            <span class="info-box-icon bg-white"><i class="fa fa-money-bill-1"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>jumlah Piutang Pajak</b></span>
                                <p>Rp{{ number_format((float) $jumlah_piutang_tahun, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                </div>

                        {{-- <div class="col-lg-4">
                            <a href="/data_piutang" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-bill-1"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b> Jumlah Piutang bulan ini</b></span>
                                    <p>Belum Terhitung</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}

                        {{-- <div class="col-lg-4">
                            <a href="/laporan_pelunasan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Laporan Pelunasan</b></span>
                                    <p>{{ $laporanpelunasan }} Data</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}
                        <div class="col-lg-4">
                            <a href="/laporan_pelunasan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-money-check-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah pelunasan Pajak</b></span>
                                    <p>Rp{{ number_format((float) $jumlah_pelunasan_tahun, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                        {{-- <div class="col-lg-4">
                            <a href="/laporan_pelunasan" style="text-decoration: none; color:black;">
                            <div class="info-box" style="width: 50px height: 100px">
                                <span class="info-box-icon bg-white"><i class="fa fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>jumlah Pelunasan bulan ini</b></span>
                                    <p>Belum Terhitung</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}

                    <div class="col-lg-4">
                        {{-- <div class="col-md-6"> --}}
                            <div class="box">
                                <div class="box-header with-border">
                                    <b class="box-title">Diagram Batang </b>
                                </div>
                                <div class="box-body">
                                    <canvas id="barChart" width="50" height="50"></canvas>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                    <div class="col-lg-4">
                        {{-- <div class="col-md-6"> --}}
                            <div class="box">
                                <div class="box-header with-border">
                                    <b class="box-title">Diagram Grafik Pembayaran Pajak</b>
                                </div>
                                <div class="box-body">
                                    <canvas id="lineChart" width="50" height="50"></canvas>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                    <div class="col-lg-4">
                        {{-- <div class="col-md-6"> --}}
                            <div class="box">
                                <div class="box-header with-border">
                                    <b class="box-title">Diagram Pie Status Penetapan</b>
                                </div>
                                <div class="box-body">
                                    <canvas id="pieChart"></canvas>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                        
                        
                        {{-- <div class="col-lg-4">
                            <a href="/data_user" style="text-decoration: none; color:black;">
                            <div class="info-box">
                                <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Jumlah User</b></span>
                                    <span>Admin: {{ $admin }}</span>
                                    <p>Petugas Penagihan: {{ $petugaspenagihan }}</p>
                                    <p>Pimpinan: {{ $pimpinan }}</p>
                                </div>
                            </div>
                            </a>
                        </div> --}}


                        {{-- <div class="col-lg-4">
                            <a href="/data_user" style="text-decoration: none; color:black;">
                                <div class="info-box">
                                    <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><b>Jumlah Petugas Penagihan</b></span>
                                        <p>Zonasi 1: {{ $p1 }} Orang</p>
                                        <p>Zonasi 2: {{ $p2 }} Orang</p>
                                        <p>Zonasi 3: {{ $p3 }} Orang</p>
                                        <p>Zonasi 4: {{ $p4 }} Orang</p>
                                        <p>{{ $petugaspenagihan }} Orang Petugas Penagihan</p>
                                    </div>
                                </div>
                            </a>
                        </div> --}}
                        {{-- <div class="col-lg-4">
                            <a href="/data_user" style="text-decoration: none; color:black;">
                            <div class="info-box">
                                <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>Jumlah Pimpinan</b></span>
                                    <p>{{ $pimpinan }} Orang Pimpinan</p>
                                </div>
                            </div>
                            </a>
                        </div> --}}
                        

                        




                    </div>
                </div>
            {{-- </div>
        </div> --}}
</div>
</section>

<script>
// const data = {
//     labels: ['Admin', 'Petugas Penagihan', 'Pimpinan'],
//     datasets: [{
//         label: 'User Roles',
//         data: [10, 15, 5], // Sesuaikan data sesuai dengan database Anda
//         backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
//         hoverOffset: 4
//     }]
// };
// const config = {
//     type: 'pie',
//     data: data,
// };

// // Render Pie Chart
// const PieChart = new Chart(
//     document.getElementById('PieChart'),
//     config
// );

      // Data Dummy
      const labels = ['Jenis Pajak A', 'Jenis Pajak B', 'Jenis Pajak C'];
        const data = [30, 50, 20];

        // Diagram Pie
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Persentase Pajak',
                    data: data,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            }
        });

        // Diagram Batang
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pajak (dalam juta)',
                    data: data,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Diagram Garis
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tren Pajak Bulanan',
                    data: data,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
</script>

@endsection