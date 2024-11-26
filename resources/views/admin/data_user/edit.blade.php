@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        User
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"> Edit Data User </h3>
            <div class="pull-right">
                <a href="{{ url('data_user') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form id="wpForm" action="{{ route('data_user.update', $user->id) }}" method="post">
                        @csrf
                        @method('put') <!-- Menambahkan method PUT -->
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                        </div>
                    
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                        </div>
                    
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                    
                        <div class="form-group">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <input type="number" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" required>
                        </div>
                    
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control" value="{{ old('alamat', $user->alamat) }}" required>
                        </div>
                    
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">- Pilih -</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="petugas_penagihan" {{ $user->role == 'petugas_penagihan' ? 'selected' : '' }}>Petugas Penagihan</option>
                            </select>
                        </div>
                    
                        <div class="form-group">
                            <label for="pembagian_zonasi">Pembagian Zonasi</label>
                            <select name="pembagian_zonasi" id="pembagian_zonasi" class="form-control">
                                <option value="">- Pilih -</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $user->pembagian_zonasi == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-paper-plane"></i> Kirim
                            </button>
                            <button type="reset" class="btn btn-danger">
                                <i class="fa fa-refresh"></i> Ulangi
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
