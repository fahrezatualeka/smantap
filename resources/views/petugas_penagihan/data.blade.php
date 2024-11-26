@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 450px;
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>Penagihan</h1>
</section>

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3500
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3500
    });
</script>
@endif

<!-- Menampilkan error validasi jika ada -->
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<section class="content">
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            Data Penagihan (Pembagian Zonasi {{ Auth::user()->pembagian_zonasi ?? 'Tidak Ditentukan' }})
        </h3>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pajak</th>
                    <th>Alamat</th>
                    <th>NPWPD</th>
                    {{-- <th>Nomor Telepon</th> --}}
                    <th>Jenis Pajak</th>
                    <th>Kategori Pajak</th>
                    {{-- <th>Tanggal Tagihan</th> --}}
                    <th>Jumlah Penagihan</th>
                    <th>Periode</th>
                    <th>Pembagian Zonasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if($dataPenagihan->isEmpty())

                <tr>
                    <td colspan="14" class="text-center">Tidak ada data penagihan.</td>
                </tr>
                @else
                @foreach($dataPenagihan as $key => $piutang)

                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $piutang->nama_pajak }}</td>
                    <td>{{ $piutang->alamat }}</td>
                    <td>{{ $piutang->npwpd }}</td>
                    {{-- <td>{{ $piutang->nomor_telepon }}</td> --}}
                    <td>{{ $piutang->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    <td>{{ $piutang->kategoriPajak->kategoripajak ?? 'N/A' }}</td>                    
                    <td>Rp{{ number_format((float) $piutang->jumlah_penagihan, 0, ',', '.') }}</td>
                    <td>{{ $piutang->periode }}</td>
                    <td>{{ $piutang->pembagian_zonasi }}</td>

                    {{-- <td>
                        <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#paymentModal{{ $piutang->id }}">
                            Belum Bayar
                        </button>
                    </td> --}}
                    <td>
                        @if($piutang->status === 'Sudah Bayar')
                            <span class="text-success">Sudah Bayar</span>
                        @else
                            <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#paymentModal{{ $piutang->id }}">
                                {{ ucfirst($piutang->status) }}
                            </button>
                        @endif
                    </td>
                    

                    <div class="modal fade" id="paymentModal{{ $piutang->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel{{ $piutang->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center" id="paymentModalLabel{{ $piutang->id }}"><b>Form Pembayaran Petugas Penagihan</b>
                                    <p>Wajib Pajak {{$piutang->nama_pajak}}</p></h4>
                                </div>
                                <div class="modal-body">

                                    {{-- <form action="{{ route('data_penagihan.updateStatus', $piutang->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH') --}}

                                    <form action="{{ route('data_penagihan.updateStatus', $piutang->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                        <input type="hidden" name="status" value="Sudah Bayar">

                                        <div class="form-group">
                                            <label for="nama_pajak">Nama Pajak</label>
                                            <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $piutang->nama_pajak }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="npwpd">NPWPD</label>
                                            <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $piutang->npwpd }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_penagihan">Jumlah Penagihan</label>
                                            <input type="text" name="jumlah_penagihan" id="jumlah_penagihan" class="form-control" value="{{ number_format($piutang->jumlah_penagihan, 0, '', '') }}" readonly>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label for="jumlah_pembayaran">Jumlah Pembayaran</label>
                                            <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran" class="form-control" required>
                                        </div> --}}
                                        <div class="form-group">
                                            <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                                            <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="buktipembayaran">Bukti Pembayaran</label>
                                            <input type="file" name="buktipembayaran" id="buktipembayaran" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="buktivisit">Bukti Visit</label>
                                            <input type="file" name="buktivisit" id="buktivisit" class="form-control" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan data ini?')">
                                            <i class="fa fa-paper-plane"></i> Kirim
                                        </button>
                                    </form>                                    

                                </div>

                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="" class="btn bg-black" target="_blank">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        <a href="" class="btn bg-black" target="_blank">
            <i class="fa-solid fa-file-pdf"></i> Export Pdf
        </a>
    </div>
</div>
</section>

<script>
    function showImageProof(url, title) {
    Swal.fire({
        title: title,
        imageUrl: url,  // Load the image directly from the URL
        imageAlt: title,
        imageWidth: 500,
        imageHeight: 500,
        showCloseButton: true,
        showConfirmButton: false,  // Remove confirm button to just show the image
        width: 'auto',
        padding: '1em',
    });
}
</script>
@endsection