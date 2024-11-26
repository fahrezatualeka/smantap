@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        <b>Jenis Pajak</b>
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
            <h3 class="box-title"> Data Jenis Pajak</h3>
            <div class="pull-right">
                <a href="{{ route('admin.jenis_pajak.create') }}" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Tambah Data
                </a>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis Pajak</th>
                        {{-- <th style="width: 124px">Aksi</th> --}}
                        <th style="width: 0px">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @if($data->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data jenis pajak.</td>
                    </tr>
                    @else
                    @foreach ($data as $key => $jenisPajak)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $jenisPajak->jenispajak }}</td>
                        <td>
                            <a href="{{ route('admin.jenis_pajak.edit', $jenisPajak->id) }}" class="btn btn-success btn-xs">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            {{-- <form action="{{ route('admin.jenis_pajak.delete', $jenisPajak->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Apakah anda yakin ingin menghapus data?')">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </form> --}}
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>

    </div>
</section>
@endsection