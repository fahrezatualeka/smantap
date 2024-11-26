@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 545px;
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Pelunasan</b>
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
        <h3 class="box-title"> Laporan Pelunasan</h3>
    </div>

    
    <div class="box-body">
        <form action="{{ route('admin.laporan_pelunasan.filter') }}" method="GET" id="filterForm">
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
                    <th>Jumlah Penagihan</th>
                    {{-- <th>Jumlah Pembayaran</th> --}}
                    <th>Tanggal Pembayaran</th>
                    <th>Bukti Pembayaran</th>
                    <th>Bukti Visit</th>
                    <th>Tempat Pembayaran</th>
                </tr>
        </thead>
            <tbody>
                @if($dataPelunasan->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada laporan pelunasan.</td>
                </tr>
                @else
                @foreach ($dataPelunasan as $key => $pelunasan)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $pelunasan->nama_pajak }}</td>
                    <td>{{ $pelunasan->alamat }}</td>
                    <td>{{ $pelunasan->npwpd }}</td>
                    {{-- <td>{{ $pelunasan->nomor_telepon }}</td> --}}
                    <td>{{ $pelunasan->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    <td>{{ $pelunasan->kategoriPajak->kategoripajak ?? 'N/A' }}</td>                    
                    <td>Rp{{ number_format((float) $pelunasan->jumlah_penagihan, 0, ',', '.') }}</td>
                    {{-- <td>Rp{{ number_format((float) $pelunasan->jumlah_pembayaran, 0, ',', '.') }}</td> --}}

                    <td>{{ $pelunasan->tanggal_pembayaran }}</td>
                    
                    <td>
                        @if($pelunasan->buktivisit)
                        <a href="javascript:void(0);" 
                        class="btn btn-warning btn-xs" 
                        onclick="showImageProof('{{ route('admin.laporan_pelunasan.showVisitProof', $pelunasan->id) }}', 'Bukti Visit')">
                        <i class="fa-solid fa-eye"></i> Lihat
                    </a>
                    @else
                    <span>Tidak ada</span>
                    @endif
                </td>
                <td>
                    @if($pelunasan->buktipembayaran)
                    <a href="javascript:void(0);" 
                    class="btn btn-warning btn-xs" 
                    onclick="showImageProof('{{ route('admin.laporan_pelunasan.showPaymentProof', $pelunasan->id) }}', 'Bukti Pembayaran')">
                    <i class="fa-solid fa-eye"></i> Lihat
                </a>
                @else
                <span>Tidak ada</span>
                @endif
            </td>
            <td>{{ $pelunasan->tempat_pembayaran }}</td>


                </tr>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="{{ route('admin.laporan_pelunasan.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.laporan_pelunasan.exportPdf') }}" class="btn bg-black">
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