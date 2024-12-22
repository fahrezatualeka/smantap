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
        <h3 class="box-title">
            Data Pelunasan (Zona {{ Auth::user()->zona ?? 'Tidak Ditentukan' }})
        </h3>
    </div>

    
    {{-- <div class="box-body">
        <form action="{{ route('petugas_penagihan.data_pelunasan.filter') }}" method="GET" id="filterForm">
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
                    <label for="bulan">Bulan:</label>
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
                        <option value="December">Desember</option>
                    </select>
                </div>
        
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;">
                        <i class="fa-solid fa-filter"></i> Filter Data
                    </button>
                </div>
            </div>
        </form>
    </div> --}}
    

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
                    {{-- <th>Zona</th> --}}
                    {{-- <th>Tagihan</th> --}}
                    <th>Periode</th>
                    {{-- <th>Jumlah Pembayaran</th> --}}
                    <th>Tanggal Pembayaran</th>
                    <th>Metode Pembayaran</th>
                    <th>Jumlah Pembayaran</th>
                    <th>Bukti Pembayaran</th>
                    <th>Bukti SSPD</th>
                    <th>Bukti Visit</th>
                    {{-- <th>Pengiriman</th> --}}
                    <th>Keterangan</th>
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
                    <td>{{ $pelunasan->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    {{-- <td>{{ $pelunasan->kategoriPajak->kategoripajak ?? 'N/A' }}</td>                     --}}
                    <td>{{ $pelunasan->telepon }}</td>
                    {{-- <td>{{ $pelunasan->zona }}</td> --}}
                    {{-- <td>Rp{{ number_format((float) $pelunasan->tagihan, 0, ',', '.') }}</td> --}}
                    <td>{{ $pelunasan->periode }}</td>

                    <td>{{ $pelunasan->tanggal_pembayaran }}</td>
                    <td>{{ $pelunasan->metode_pembayaran }}</td>
                    <td>{{ $pelunasan->jumlah_pembayaran }}</td>
                    
                <td>
                    @if($pelunasan->buktipembayaran)
                    <a href="javascript:void(0);" 
                    class="btn btn-warning btn-xs" 
                    onclick="showImageProof('{{ route('admin.data_pelunasan.showPaymentProof', $pelunasan->id) }}', 'Bukti Pembayaran')">
                    <i class="fa-solid fa-eye"></i> Lihat
                </a>
                @else
                <span>Tidak ada</span>
                @endif
            </td>
                <td>
                    @if($pelunasan->buktisspd)
                    <a href="javascript:void(0);" 
                    class="btn btn-warning btn-xs" 
                    onclick="showImageProof('{{ route('admin.data_pelunasan.showPaymentProof', $pelunasan->id) }}', 'Bukti SSPD')">
                    <i class="fa-solid fa-eye"></i> Lihat
                </a>
                @else
                <span>Tidak ada</span>
                @endif
            </td>
            <td>
                @if($pelunasan->buktivisit)
                <a href="javascript:void(0);" 
                class="btn btn-warning btn-xs" 
                onclick="showImageProof('{{ route('admin.data_pelunasan.showVisitProof', $pelunasan->id) }}', 'Bukti Visit')">
                <i class="fa-solid fa-eye"></i> Lihat
            </a>
            @else
            <span>Tidak ada</span>
            @endif
        </td>
            {{-- <td>{{ $pelunasan->tempat_pembayaran }}</td> --}}
            <td>{{ $pelunasan->keterangan }}</td>


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