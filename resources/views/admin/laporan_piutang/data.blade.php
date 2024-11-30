@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 600px;
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
            timer: 2250
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
            timer: 2250
        });
    </script>
@endif


    <div class="box-header with-border">
        <h3 class="box-title"> Laporan Piutang</h3>
    </div>

    
    <div class="box-body">
        <form action="{{ route('admin.laporan_piutang.filter') }}" method="GET" id="filterForm">
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
                    <th>Jenis Pajak</th>
                    <th>Kategori Pajak</th>
                    <th>Nomor Telepon</th>
                    <th>Pembagian Zonasi</th>
                    <th>Jumlah Penagihan</th>
                    <th>Periode</th>
                </tr>
        </thead>
            <tbody>
                @if($laporanPiutang->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada laporan piutang.</td>
                </tr>
                @else
                @foreach ($laporanPiutang as $key => $piutang)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $piutang->nama_pajak }}</td>
                    <td>{{ $piutang->alamat }}</td>
                    <td>{{ $piutang->npwpd }}</td>
                    <td>{{ $piutang->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    <td>{{ $piutang->kategoriPajak->kategoripajak ?? 'N/A' }}</td>
                    <td>{{ $piutang->nomor_telepon }}</td>
                    <td>{{ $piutang->pembagian_zonasi }}</td>
                    <td>Rp{{ number_format((float) $piutang->jumlah_penagihan, 0, ',', '.') }}</td>
                    <td>{{ $piutang->periode }}</td>
                </tr>
                    {{-- <td>
                        <a href="{{ route('admin.data_wajibpajak.edit', $datawajibpajak->id) }}" class="btn btn-success btn-xs">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.data_wajibpajak.delete', $datawajibpajak->id) }}" method="POST" style="display: inline-block;">


                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Apakah anda yakin ingin menghapus data?')">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td> --}}
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="{{ route('admin.laporan_piutang.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.laporan_piutang.exportPdf') }}" class="btn bg-black">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>
</section>
@endsection