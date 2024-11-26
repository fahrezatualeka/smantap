@extends('layout/template')
@section('content')

<style>
.table-responsive {
    max-height: 530px;
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
    

    <div class="box-header with-border">
        <h3 class="box-title">Data Piutang</h3>
        <div class="pull-right">
            <form action="{{ route('admin.data_piutang.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-file-import"></i> Import Piutang dari Data Penetapan
                </button>
            </form>
            
            
            
        </div>
    </div>
    {{-- onclick="return confirm('Apakah Anda yakin ingin mengimpor data piutang yang belum bayar?')" --}}
    

    
    <div class="box-body table-responsivey">
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
    
                <!-- Button Filter -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;"><i class="fa-solid fa-filter"></i> Filter Data</button>
                </div>
            </div>
            
        </form>
        
    </div>

    <form action="{{ route('admin.data_piutang.saveZonasi') }}" method="POST">
        @csrf
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
                    <th>Pembagian Zonasi</th>
                </tr>
            </thead>
            <tbody>
                @if($data->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data piutang yang di import.</td>
                </tr>
                @else
                @foreach ($data as $key => $datapiutang)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $datapiutang->nama_pajak }}</td>
                    <td>{{ $datapiutang->alamat }}</td>
                    <td>{{ $datapiutang->npwpd }}</td>
                    {{-- <td>{{ $datapiutang->nomor_telepon }}</td> --}}
                    <td>{{ $datapiutang->jenisPajak->jenispajak ?? '-' }}</td>
                    <td>{{ $datapiutang->kategoriPajak->kategoripajak ?? '-' }}</td>
                    <td>Rp{{ number_format((float) $datapiutang->jumlah_penagihan, 0, ',', '.') }}</td>
                    <td>{{ $datapiutang->periode }}</td>
                    <td>
                        @if(is_null($datapiutang->pembagian_zonasi))
                            <select name="pembagian_zonasi[{{ $datapiutang->id }}]" class="form-control">
                                <option value="">- Pilih -</option>
                                @for ($i = 1; $i <= 4; $i++)
                                <option value="{{ $i }}" {{ $datapiutang->pembagian_zonasi == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                                @endfor
                            </select>
                        @else
                            <p>{{ $datapiutang->pembagian_zonasi }}</p>
                        @endif
                    </td>
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
        
        <a href="{{ route('admin.data_piutang.exportPdf') }}" class="btn bg-black">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <div class="pull-right">
            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan pembagian zonasi tersebut?')"><i class="fa-solid fa-floppy-disk"></i> Simpan Zonasi</button>
        </div>
    </div>
    </form>
</div>
</section>

<script>
    document.querySelector('form[action="{{ route('admin.data_piutang.import') }}"]').addEventListener('submit', function(e) {
        console.log('Form submitted!');
    });
</script>


@endsection