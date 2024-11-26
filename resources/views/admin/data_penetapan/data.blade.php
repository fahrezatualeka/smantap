@extends('layout/template')
@section('content')




<style>
    .table-responsive {
        max-height: 345px;
        max-height: 280px;
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Penetapan</b>
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
            timer: 25000
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
            timer: 25000
        });
    </script>
@endif




    <div class="box-header with-border">
        <h3 class="box-title"> Data Penetapan</h3>
    </div>



    <div class="box-body table-responsive">
        <form action="{{ route('admin.data_penetapan.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Baris Pertama -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div class="form-group" style="flex: 1; margin-right: 10px;">
                    <label for="import_data_penetapan"><i class="fa-solid fa-file-import"></i> Import Data Penetapan</label>
                    <input type="file" name="file" id="import_data_penetapan" class="form-control" required>
                </div>
                <div class="form-group">
                    <a href="{{ asset('template/template_penetapan.xlsx') }}" class="btn bg-blue">
                        <i class="fa-solid fa-file-excel"></i> Download Template Excel
                    </a>
                </div>
            </div>
    
            <!-- Baris Kedua -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div class="form-group" style="flex: 1; margin-right: 10px;">
                    <label for="bulan">Pilih Bulan</label>
                    <select name="bulan" id="bulan" class="form-control" required>
                        <option value="">- Pilih -</option>
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
                        <option value="December">Desember</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="tahun">Pilih Tahun</label>
                    <select name="tahun" id="tahun" class="form-control" required>
                        <option value="">- Pilih -</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
            </div>
    
            <!-- Tombol Submit -->
            <div>
                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan data ini?')">
                    <i class="fa fa-paper-plane"></i> Kirim
                </button>
                <a href="{{ route('admin.data_penetapan.create') }}" class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> Tambah Data
                </a>
            </div>
        </form>
    </div>
    


    
    <div class="box-body">
        <form action="{{ route('admin.data_penetapan.filter') }}" method="GET" id="filterForm">
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
    
                <!-- Filter Kategori Pajak (jika ada) -->
                {{-- <div class="col-md-2">
                    <label for="kategori_pajak_id">Kategori Pajak:</label>
                    <select name="kategori_pajak_id" class="form-control" id="kategori_pajak_id">
                        <option value="">- Semua -</option>
                        <!-- Menambahkan pilihan kategori pajak jika perlu -->
                        <option value="1" {{ request()->kategori_pajak_id == '1' ? 'selected' : '' }}>Kategori 1</option>
                        <option value="2" {{ request()->kategori_pajak_id == '2' ? 'selected' : '' }}>Kategori 2</option>
                    </select>
                </div> --}}
        
                <!-- Button Filter -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;">
                        <i class="fa-solid fa-filter"></i> Filter Data
                    </button>
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
                    {{-- <th>Nomor Telepon</th> --}}
                    <th>Jenis Pajak</th>
                    <th>Kategori Pajak</th>
                    <th>Jumlah Penagihan</th>
                    <th>Periode</th>
                    <th>Status</th>
                    {{-- <th>Bukti</th> --}}
                    {{-- <th>Bukti Pembayaran</th> --}}
                    {{-- <th style="width: 180px">Aksi</th> --}}
                    <th style="width: 124px">Aksi</th>
                </tr>
        </thead>
            <tbody>
                @if($dataPenetapan->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data penetapan.</td>
                </tr>
                @else
                @foreach ($dataPenetapan as $key => $penetapan)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $penetapan->nama_pajak }}</td>
                    <td>{{ $penetapan->alamat }}</td>
                    <td>{{ $penetapan->npwpd }}</td>
                    {{-- <td>{{ $pelunasan->nomor_telepon }}</td> --}}
                    <td>{{ $penetapan->jenisPajak->jenispajak ?? '-' }}</td>
                    <td>{{ $penetapan->kategoriPajak->kategoripajak ?? '-' }}</td>
                    <td>Rp{{ number_format((float) $penetapan->jumlah_penagihan, 0, ',', '.') }}</td>
                    <td>{{ $penetapan->periode }}</td>
                    
                    
                        <td>
                            @if($penetapan->status === 'Sudah Bayar')
                                <span class="text-success">Sudah Bayar</span>
                            @else
                                <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#paymentModal{{ $penetapan->id }}">
                                    {{ ucfirst($penetapan->status) }}
                                </button>
                            @endif
                        </td>
                        
                        
                        
                    
                    <div class="modal fade" id="paymentModal{{ $penetapan->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel{{ $penetapan->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center" id="paymentModalLabel{{ $penetapan->id }}"><b>Form Pembayaran Admin</b>
                                    <p>Wajib Pajak {{$penetapan->nama_pajak}}</p></h4>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.data_penetapan.updateStatus', $penetapan->id) }}" method="POST" enctype="multipart/form-data">

                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Sudah Bayar">
                                        <div class="form-group">
                                            <label for="nama_pajak">Nama Pajak</label>
                                            <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $penetapan->nama_pajak }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="npwpd">NPWPD</label>
                                            <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $penetapan->npwpd }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="jumlah_penagihan">Jumlah Penagihan</label>
                                            <input type="text" name="jumlah_penagihan" id="jumlah_penagihan" class="form-control" value="{{ number_format($penetapan->jumlah_penagihan, 0, '', '') }}" readonly>
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
                    </div>
                    

                    


                    
                    <td>
                         
                        <a href="{{ route('admin.data_penetapan.edit', $penetapan->id) }}" class="btn btn-success btn-xs">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.data_penetapan.delete', $penetapan->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Apakah anda yakin ingin menghapus data? karena data yang anda hapus akan otomatis terhapus di data piutang dan laporan pelunasan.')">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="{{ route('admin.data_penetapan.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.data_penetapan.exportPdf') }}" class="btn bg-black">
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
    </script>
@endsection