@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 632px;
        /* max-height: 525px; */
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Kategori Pajak</b>
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
                timer: 2150
            });
        </script>
        @endif
        
        <div class="box-header with-border">
            <h3 class="box-title"> Data Kategori Pajak</h3>
            <div class="pull-right">
                <a href="{{ route('admin.kategori_pajak.create') }}" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Tambah Data
                </a>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('admin.kategori_pajak.filter') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search">Pencarian:</label>
                        <input type="text" name="search" class="form-control" id="search" placeholder="- Semua -" value="{{ request()->search }}">
                    </div>
            
                    <div class="col-md-2">
                        <label for="pembagian_zonasi">Jenis Pajak:</label>
                        <select name="jenis_pajak_id" class="form-control" id="pembagian_zonasi">
                            <option value="">- Semua -</option>
                            <option value="1" {{ request()->jenis_pajak_id == '1' ? 'selected' : '' }}>Hotel</option>
                            <option value="2" {{ request()->jenis_pajak_id == '2' ? 'selected' : '' }}>Restoran</option>
                            <option value="3" {{ request()->jenis_pajak_id == '3' ? 'selected' : '' }}>Hiburan</option>
                        </select>
                    </div>
            
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-default" style="margin-top: 25px;"><i class="fa-solid fa-filter"></i> Filter Data</button>
                    </div>
                </div>
            </form>
            
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis Pajak</th>
                        <th>Nama Kategori Pajak</th>
                        {{-- <th style="width: 124px">Aksi</th> --}}
                        {{-- <th style="width: 0px">Aksi</th> --}}
                    </tr>
                </thead>

                <tbody>
                    @if($data->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data kategori pajak.</td>
                    </tr>
                    @else
                    @foreach ($data as $key => $kategoriPajak)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $kategoriPajak->JenisPajak->jenispajak ?? 'N/A' }}</td>
                        <td>{{ $kategoriPajak->kategoripajak }}</td>
                        {{-- <td>
                            <a href="{{ route('admin.kategori_pajak.edit', $kategoriPajak->id) }}" class="btn btn-success btn-xs">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.kategori_pajak.delete', $kategoriPajak->id) }}" method="POST" style="display: inline-block;">
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

    </div>
</section>
@endsection