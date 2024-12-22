@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        <b>Kelola Pesan WhatsApp</b>
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
        
        {{-- <div class="box-header with-border">
            <h3 class="box-title"> Data Kelola Pesan WhatsApp</h3>
            <div class="pull-right">
                <a href="{{ route('admin.kelola_pesan_whatsapp.create') }}" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Tambah Data
                </a>
            </div>
        </div> --}}

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Pesan</th>
                        <th>Deskripsi</th>
                        <th style="width: 0px">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @if($datawhatsapp->isEmpty())
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data kelola pesan whatsapp.</td>
                    </tr>
                    @else
                    @foreach ($datawhatsapp as $key => $whatsapp)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $whatsapp->jenis_pesan }}</td>
                        <td>{{ $whatsapp->deskripsi }}</td>

                        <td>
                            <a href="{{ route('admin.kelola_pesan_whatsapp.edit', $whatsapp->id) }}" class="btn btn-success btn-xs">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                    </tr>
                    @endforeach
                    @endif
                </tbody>

            </table>
        </div>

    </div>
</section>
@endsection