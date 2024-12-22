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
                    {{-- <h3 class="box-title">Statistik Sistem Aplikasi Penagihan Piutang Pajak</h3> --}}
                    <h3 class="box-title">Statistik Sistem Aplikasi Penagihan Piutang Pajak</h3>
                </div>
                <div class="box-body ">
                    <div class="row">
                        <div class="col-lg-3">
                            <a href="/data_wajibpajak" style="text-decoration: none; color:black;">
                                <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                                <span class="info-box-icon" style="background-color: #3EA99F; color: white; height: 212px;">
                                    <i class="fa fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>      
                                            Data Wajib Pajak
                                    </b></span>
                                    <p>{{ $datawajibpajak }}</p>
                                </div>
                            </div>
                        </a>
                    </div>




                <div class="col-lg-3">
                    <a href="/jenis_pajak" style="text-decoration: none; color:black;">
                        <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                        <span class="info-box-icon" style="background-color: #00827F; color: white; height: 212px;">
                            <i class="fa fa-database"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text"><b>Variabel Pajak</b></span>
                            <p>{{ $jenispajak }} Jenis Pajak</p>
                        </div>
                    </div>
                </a>
            </div>
                <div class="col-lg-3">
                    <a href="/data_user" style="text-decoration: none; color:black;">
                        <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                        <span class="info-box-icon" style="background-color: #3C565B; color: white; height: 212px;">
                            <i class="fa fa-user"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text"><b>Data User</b></span>
                            <p>{{ $datauser }} User</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3">
                <a href="/data_piutang" style="text-decoration: none; color:black;">

<div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">
                    <span class="info-box-icon" style="background-color: #dd1717; color: white; height: 212px;">
                        <i class="fa fa-file-invoice-dollar"></i>

                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Data Piutang</b></span>
                        <p> Zona 1: {{$datapiutang1}}</p>
                        <p> Zona 2: {{$datapiutang2}}</p>
                        <p> Zona 3: {{$datapiutang3}}</p>
                        <p> Zona 4: {{$datapiutang4}}</p>
                        <p>Total Keseluruhan: {{$datapiutang}}</p>
                    </div>
                </div>
            </a>
        </div>



                    <div class="col-lg-3">
                        <a href="/laporan_transfer" style="text-decoration: none; color:black;">
                            <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                            <span class="info-box-icon" style="background-color: #6eaa5e; color: white; height: 212px;">
                                <i class="fa fa-money-bill-transfer"></i>

                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>Transfer WP</b></span>
                                <p> Zona 1: {{$laporantransfer1}}</p>
                                <p> Zona 2: {{$laporantransfer2}}</p>
                                <p> Zona 3: {{$laporantransfer3}}</p>
                                <p> Zona 4: {{$laporantransfer4}}</p>
                                <p>Total Keseluruhan: {{ $laporantransfer }}</p>
                            <p>Jumlah Pembayaran: Rp{{ number_format($jumlahtransfer, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                    <div class="col-lg-3">
                        <a href="/laporan_tunai" style="text-decoration: none; color:black;">
                            <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                            <span class="info-box-icon" style="background-color: #469536; color: white; height: 212px;">
                                <i class="fa fa-handshake"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>Tunai Petugas</b></span>
                                <p> Zona 1: {{$laporantunai1}}</p>
                                <p> Zona 2: {{$laporantunai2}}</p>
                                <p> Zona 3: {{$laporantunai3}}</p>
                                <p> Zona 4: {{$laporantunai4}}</p>
                                <p>Total Keseluruhan: {{ $laporantunai }}</p>
                            <p>Jumlah Pembayaran: Rp{{ number_format($jumlahtunai, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                    <div class="col-lg-3">
                        <a href="/laporan_konfirmasi" style="text-decoration: none; color:black;">
                            <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                            <span class="info-box-icon" style="background-color: #008000; color: white; height: 212px;">
                                <i class="fa fa-clipboard-check"></i>

                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>Konfirmasi</b></span>
                                <p> Zona 1: {{$laporankonfirmasi1}}</p>
                                <p> Zona 2: {{$laporankonfirmasi2}}</p>
                                <p> Zona 3: {{$laporankonfirmasi3}}</p>
                                <p> Zona 4: {{$laporankonfirmasi4}}</p>
                                <p>Total Keseluruhan: {{ $laporankonfirmasi }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                    <div class="col-lg-3">
                        <a href="/laporan_penutupan" style="text-decoration: none; color:black;">
                            <div class="info-box" style=" width: 100%; min-height: 212px; border: 1px solid #ddd;">

                            <span class="info-box-icon" style="background-color: #075207; color: white; height: 212px;">
                                <i class="fa fa-building-lock"></i>

                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text"><b>WP Nonaktif</b></span>
                                <p> Zona 1: {{$laporanpenutupan1}}</p>
                                <p> Zona 2: {{$laporanpenutupan2}}</p>
                                <p> Zona 3: {{$laporanpenutupan3}}</p>
                                <p> Zona 4: {{$laporanpenutupan4}}</p>
                                <p>Total Keseluruhan: {{ $laporanpenutupan }}</p>
                            </div>
                        </div>
                    </a>
                </div>

                    <div class="col-lg-8">
                            <div class="box">
                                
                                <div class="box-header with-border" >
                                    <b class="box-title">Diagram Grafik Jumlah Pembayaran Wajib Pajak</b>
                                </div>
                                <div class="box-body">
                                    <canvas id="lineChart" width="100" height="45"></canvas>
                                    
                                </div>
                                <p style="text-align: center">Tahun 2024</p>
                            </div>

                    </div>
                    <div class="col-lg-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <b class="box-title">Diagram Lingkaran Pembayaran Pajak</b>
                                </div>
                                <div class="box-body">
                                    <canvas id="pieChart"></canvas>
                                </div>
                            </div>
                    </div>

                    </div>
                </div>
</div>
</section>

<script>
    const labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const dataPiutang = @json($grafikTransfer);
    const dataPelunasan = @json($grafikTunai);

    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Transfer WP',
                data: dataPiutang,
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
            },
            {
                label: 'Tunai ',
                data: dataPelunasan,
                borderColor: '#FF5733',
                backgroundColor: 'rgba(255, 87, 51, 0.2)',
                fill: true,
            }
        ]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

    const labelsP = ['Data Piutang', 'Transfer WP', 'Tunai ', 'Konfirmasi', 'WP Nonaktif'];
    const dataStatus = [{{ $lingkaranpiutang }}, {{ $lingkarantransfer }}, {{ $lingkarantunai }}, {{ $lingkarankonfirmasi }}, {{ $lingkaranpenutupan }}];

    // Diagram Pie
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: labelsP,
            datasets: [{
                label: 'Jumlah',
                data: dataStatus,
                backgroundColor: ['#dd1717', '#6eaa5e', '#469536', '#008000', '#075207'],
            }]
        }
    });
</script>

@endsection