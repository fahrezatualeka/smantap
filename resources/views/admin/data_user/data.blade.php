@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        <b>User</b>
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
            <h3 class="box-title">Data User</h3>
            <div class="pull-right">
                <a href="{{ url('data_user/add') }}" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Tambah Data
                </a>
            </div>
        </div>

        <div class="box-body">
            <form action="{{ route('admin.data_user.filter') }}" method="GET" id="filterForm">
                <div class="row">
                    <!-- Filter Kategori Pajak -->
                    <div class="col-md-3">
                        <label for="search">Pencarian:</label>
                        <input type="text" name="search" class="form-control" id="search" placeholder="- Semua -" value="{{ request()->search }}">
                    </div>
                    <div class="col-md-2">
                        <label for="role">Role:</label>
                        <select name="role" class="form-control" id="role">
                            <option value="">- Semua -</option>
                            <option value="admin" {{ request()->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="petugas_penagihan" {{ request()->role == 'petugas_penagihan' ? 'selected' : '' }}>Petugas Penagihan</option>
                            <option value="pimpinan" {{ request()->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                        </select>
                    </div>
    
        
                    <div class="col-md-2">
                        <label for="pembagian_zonasi">Pembagian Zonasi:</label>
                        <select name="pembagian_zonasi" class="form-control" id="pembagian_zonasi">
                            <option value="">- Semua -</option>
                            <option value="1" {{ request()->pembagian_zonasi == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ request()->pembagian_zonasi == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ request()->pembagian_zonasi == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ request()->pembagian_zonasi == '4' ? 'selected' : '' }}>4</option>
                        </select>
                    </div>
        
                    <!-- Button Filter -->
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
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Nomor Telepon</th>
                        <th>Alamat</th>
                        <th>Role</th>
                        <th>Pembagian Zonasi</th>
                        {{-- <th style="width: 0px">Aksi</th> --}}
                    </tr>
                </thead>

                <tbody>
                    @if($data->isEmpty())
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data user.</td>
                    </tr>
                    @else
                    @foreach ($data as $key => $users)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $users->nama }}</td>
                        <td>{{ $users->username }}</td>
                        <td>{{ $users->nomor_telepon }}</td>
                        <td>{{ $users->alamat }}</td>
                        <td>{{ $users->role }}</td>
                        <td>{{ $users->pembagian_zonasi }}</td>
                        {{-- <td>
                            <a href="{{ url('admin.data_user/edit/'.$users->id) }}" class="btn btn-success btn-xs">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <form action="{{ url('admin.data_user/delete/'.$users->id) }}" method="post" style="display: inline-block;">
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

<script>
    // Set pilihan jenis ke 'semua' saat halaman dimuat ulang
    window.onload = function() {
        document.getElementById('jenis').value = '{{ session('jenis') ?? 'semua' }}';
    };
</script>
@endsection
