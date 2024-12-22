@extends('layout/template')
@section('content')




<style>
    .table-responsive {
        max-height: 365px;
        /* max-height: 280px; */
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Piutang</b>
    </h1>
</section>
<section class="content">
<div class="box">

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 5000
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 5000
        });
    </script>
@endif




    <div class="box-header with-border">
        <h3 class="box-title"> Data Piutang</h3>
    </div>



    <div class="box-body table-responsive">
        <form action="{{ route('admin.data_piutang.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Baris Pertama -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div class="form-group" style="flex: 1; margin-right: 10px;">
                    <label for="import_data_piutang"><i class="fa-solid fa-file-import"></i> Import Data Piutang</label>
                    <input type="file" name="file" id="import_data_piutang" class="form-control" required>
                </div>
                <div class="form-group">
                    <a href="{{ asset('template/template_piutang.xlsx') }}" class="btn bg-blue">
                        <i class="fa-solid fa-file-excel"></i> Download Template Excel
                    </a>
                </div>
            </div>
    
            <!-- Baris Kedua -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div class="form-group" style="flex: 1; margin-right: 10px;">
                    <label for="bulan">Pilih Bulan</label>
                    <select name="bulan" id="bulan" class="form-control" required>
                        <option value="">- Pilih Bulan -</option>
                        <option value="January">Januari</option>
                        <option value="February">Februari</option>
                        <option value="March">Maret</option>
                        <option value="April">April</option>
                        <option value="May">Mei</option>
                        <option value="June">Juni</option>
                        <option value="July">Juli</option>
                        <option value="August">Agustus</option>
                        <option value="September">September</option>
                        <option value="October">Oktober</option>
                        <option value="November">November</option>
                        <option value="December" selected>Desember</option>
                    </select>
                </div>
                {{-- <div class="form-group" style="flex: 1;">
                    <label for="tahun">Pilih Tahun</label>
                    <select name="tahun" id="tahun" class="form-control" required>
                        <option value="2024">2024</option>
                    </select>
                </div> --}}
            </div>
    
            <!-- Tombol Submit -->
            <div>
                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan data ini?')">
                    <i class="fa fa-paper-plane"></i> Kirim
                </button>
                {{-- <a href="{{ route('admin.data_piutang.create') }}" class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> Tambah Data
                </a> --}}
                {{-- <a href="{{ route('admin.data_piutang.create') }}" class="btn btn-warning pull-right">
                    <i class="fa fa-comment-dollar"></i> Kirim Notifikasi Pembayaran ke Wajib Pajak
                </a> --}}
            </div>
        </form>
    </div>
    


    
    <div class="box-body">
        <form action="{{ route('admin.data_piutang.filter') }}" method="GET" id="filterForm">
            <div class="row">
                <!-- Filter Pencarian -->
                <div class="col-md-3">
                    <label for="search">Pencarian:</label>
                    <input type="text" name="search" class="form-control" id="search" placeholder="- Semua -" value="{{ request()->search }}">
                </div>
    
                <!-- Filter Jenis Pajak -->
                <div class="col-md-2">
                    <label for="jenis_pajak_id">Jenis Pajak:</label>
                    <select name="jenis_pajak_id" class="form-control" id="jenis_pajak_id">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->jenis_pajak_id == '1' ? 'selected' : '' }}>Hotel</option>
                        <option value="2" {{ request()->jenis_pajak_id == '2' ? 'selected' : '' }}>Restoran</option>
                        <option value="3" {{ request()->jenis_pajak_id == '3' ? 'selected' : '' }}>Hiburan</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="zona">Zona:</label>
                    <select name="zona" class="form-control" id="zona">
                        <option value="">- Semua -</option>
                        @foreach(range(1, 4) as $zona)
                            <option value="{{ $zona }}" {{ request('zona') == $zona ? 'selected' : '' }}>
                                {{ $zona }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="bulan">Periode Piutang Pajak:</label>
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="">- Semua -</option>
                        <option value="January" {{ request()->bulan == 'January' ? 'selected' : '' }}>Januari</option>
                        <option value="February" {{ request()->bulan == 'February' ? 'selected' : '' }}>Februari</option>
                        <option value="March" {{ request()->bulan == 'March' ? 'selected' : '' }}>Maret</option>
                        <option value="April" {{ request()->bulan == 'April' ? 'selected' : '' }}>April</option>
                        <option value="May" {{ request()->bulan == 'May' ? 'selected' : '' }}>Mei</option>
                        <option value="June" {{ request()->bulan == 'June' ? 'selected' : '' }}>Juni</option>
                        <option value="July" {{ request()->bulan == 'July' ? 'selected' : '' }}>Juli</option>
                        <option value="August" {{ request()->bulan == 'August' ? 'selected' : '' }}>Agustus</option>
                        <option value="September" {{ request()->bulan == 'September' ? 'selected' : '' }}>September</option>
                        <option value="October" {{ request()->bulan == 'October' ? 'selected' : '' }}>Oktober</option>
                        <option value="November" {{ request()->bulan == 'November' ? 'selected' : '' }}>November</option>
                        <option value="December" {{ request()->bulan == 'December' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>

            </div>
        </form>
    </div>
    

    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pajak</th>
                    <th>Alamat</th>
                    <th>NPWPD</th>
                    <th>Jenis Pajak</th>
                    {{-- <th>Kategori Pajak</th> --}}
                    <th>Telepon</th>
                    <th>Zona</th>
                    {{-- <th>Tagihan</th> --}}
                    <th>Periode</th>
                    {{-- <th>Status</th> --}}
                    {{-- <th>Bukti</th> --}}
                    {{-- <th>Bukti Pembayaran</th> --}}
                    {{-- <th style="width: 180px">Aksi</th> --}}
                    {{-- <th style="width: 124px">Aksi</th> --}}
                </tr>
        </thead>
            <tbody>
                @if($dataPiutang->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data piutang.</td>
                </tr>
                @else
                @foreach ($dataPiutang as $key => $piutang)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $piutang->nama_pajak }}</td>
                    <td>{{ $piutang->alamat }}</td>
                    <td>{{ $piutang->npwpd }}</td>
                    <td>{{ $piutang->jenisPajak->jenispajak ?? '-' }}</td>
                    {{-- <td>{{ $piutang->kategoriPajak->kategoripajak ?? '-' }}</td> --}}
                    <td>{{ $piutang->telepon }}</td>
                    <td>{{ $piutang->zona }}</td>
                    {{-- <td>Rp{{ number_format((float) $piutang->tagihan, 0, ',', '.') }}</td> --}}
                    <td>{{ $piutang->periode }}</td>
                    
                    
                        {{-- <td>
                            @if($piutang->status === 'Sudah Bayar')
                                <span class="text-success">Sudah Bayar</span>
                            @else
                                <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#paymentModal{{ $piutang->id }}">
                                    {{ ucfirst($piutang->status) }}
                                </button>
                            @endif
                        </td> --}}
                        
                        
                        
                    
                    {{-- <div class="modal fade" id="paymentModal{{ $piutang->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel{{ $piutang->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center" id="paymentModalLabel{{ $piutang->id }}"><b>Form Pembayaran Admin</b>
                                    <p>Wajib Pajak {{$piutang->nama_pajak}}</p></h4>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.data_piutang.updateStatus', $piutang->id) }}" method="POST" enctype="multipart/form-data">

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
                                        <div class="form-group">
                                            <label for="periode">Periode</label>
                                            <input type="text" name="periode" id="periode" class="form-control" value="{{ $piutang->periode }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_pembayaran">Jumlah Pembayaran</label>
                                            <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                                            <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="bukti_pembayaran">Bukti Pembayaran</label>
                                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="bukti_visit">Bukti Visit</label>
                                            <input type="file" name="bukti_visit" id="bukti_visit" class="form-control" required>
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
                    </div> --}}
                    

                    


                    
                    {{-- <td>
                         
                        <a href="{{ route('admin.data_piutang.edit', $piutang->id) }}" class="btn btn-success btn-xs">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.data_piutang.delete', $piutang->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Apakah anda yakin ingin menghapus data? karena data yang anda hapus akan otomatis terhapus di data piutang dan laporan pelunasan.')">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td> --}}
                </tr>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="{{ route('admin.data_piutang.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.data_piutang.exportPdf', [
    'search' => request('search'),
    'jenis_pajak_id' => request('jenis_pajak_id'),
    'bulan' => request('bulan'),
    'zona' => request('zona'),
]) }}" class="btn bg-black">
    <i class="fa-solid fa-file-pdf"></i> Export PDF
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

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filterForm");

    // Trigger form submit on input or select change
    form.querySelectorAll("input, select").forEach(element => {
        element.addEventListener("change", function () {
            form.submit();
        });

        // Untuk input teks, deteksi ketika pengguna berhenti mengetik
        if (element.type === "text") {
            let typingTimer;
            element.addEventListener("keyup", function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => form.submit(), 500); // Submit setelah 500ms berhenti mengetik
            });
        }
    });
});
    </script>
@endsection