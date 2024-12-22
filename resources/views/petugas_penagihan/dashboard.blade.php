@extends('layout/template')

@section('content')

<style>
    .table-responsive {
        max-height: 150px;
        overflow-y: auto;
    }
</style>

<section class="content-header">
    <h1><b>Dashboard</b></h1>
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

    <div class="box-header with-border">
        <h3 class="box-title">Statistik Dashboard Petugas Penagihan (Zona {{ Auth::user()->zona ?? 'Tidak Ditentukan' }})</h3>
    </div>
    <div class="box-body">
        <div class="row">

        <div class="col-lg-12">
            <a href="{{ url('/data_penagihan?zona=' . Auth::user()->zona) }}" style="text-decoration: none; color:black;">
                <div class="info-box" style=" width: 100%; border: 1px solid #ddd; height: 121px;">

                <span class="info-box-icon" style="background-color: #dd1717; color: white; height: 121px;">
                    <i class="fa fa-file-invoice-dollar"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Data Piutang</b></span>
                    <p>{{$datapenagihan}}</p>
                </div>
            </div>
        </a>
    </div>
            
            <div class="col-lg-3">
                <a href="{{ url('/data_transfer?zona=' . Auth::user()->zona) }}" style="text-decoration: none; color:black;">
                <div class="info-box" style=" width: 100%; border: 1px solid #ddd; height: 121px;">
                    <span class="info-box-icon" style="background-color: #6eaa5e; color: white; height: 121px;">
                        <i class="fa fa-money-bill-transfer"></i>

                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Transfer WP</b></span>
                        <p>{{ $laporantransfer }}</p>
                    <p>Jumlah pembayaran: Rp{{ number_format($jumlahtransfer, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
        </div>
            <div class="col-lg-3">
                <a href={{ url('/data_tunai?zona=' . Auth::user()->zona) }}"" style="text-decoration: none; color:black;">
                <div class="info-box" style=" width: 100%; border: 1px solid #ddd; ">
                    <span class="info-box-icon" style="background-color: #469536; color: white; height: 121px;">
                        <i class="fa fa-handshake"></i>

                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Tunai Petugas</b></span>
                        <p>{{ $laporantunai}}</p>
                        <p>Jumlah belum setor: Rp{{ number_format($belumSetor, 0, ',', '.') }}</p>
                        <p>Jumlah setor di kasda: Rp{{ number_format($setorKasda, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
        </div>
            <div class="col-lg-3">
                <a href={{ url('/data_konfirmasi?zona=' . Auth::user()->zona) }}" style="text-decoration: none; color:black;">
                <div class="info-box" style=" width: 100%; border: 1px solid #ddd; height: 121px;">
                    <span class="info-box-icon" style="background-color: #008000; color: white; height: 121px;">
                        <i class="fa fa-clipboard-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Konfirmasi</b></span>
                        <p>{{ $laporankonfirmasi }}</p>
                    </div>
                </div>
            </a>
        </div>
            <div class="col-lg-3">
                <a href={{ url('/data_penutupan?zona=' . Auth::user()->zona) }}" style="text-decoration: none; color:black;">
                <div class="info-box" style=" width: 100%; border: 1px solid #ddd; height: 121px;">
                    <span class="info-box-icon" style="background-color: #075207; color: white; height: 121px;">
                        <i class="fa fa-building-lock"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b>WP Nonaktif</b></span>
                        <p>{{ $laporanpenutupan }}</p>
                    </div>
                </div>
            </a>
        </div>

            <div class="col-lg-8">
                    <div class="box">
                        <div class="box-header with-border">
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
                label: 'Tunai Petugas',
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

const labelsP = ['Data Piutang', 'Transfer WP', 'Tunai Petugas', 'Konfirmasi', 'WP Nonaktif'];
    const dataStatus = [{{ $lingkaranpenagihan }}, {{ $lingkarantransfer }}, {{ $lingkarantunai }}, {{ $lingkarankonfirmasi }}, {{ $lingkaranpenutupan }}];

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