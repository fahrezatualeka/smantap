@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>Profil</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i></a></li>
        <li class="active">Profil</li>
    </ol>
</section>

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
@endif


<section class="content">
    <div class="box box-solid">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#profile" data-toggle="tab" aria-expanded="false">Profil</a></li>
                <li><a href="#editprofil" data-toggle="tab" aria-expanded="true">Edit Akun</a></li>
            </ul>
            <div class="tab-content">

                @if(auth()->check())
                <div class="tab-pane active" id="profile">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <br>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Nama Lengkap</b> <span class="pull-right">{{ auth()->user()->nama }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Username</b> <span class="pull-right">{{ auth()->user()->username }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Telepon</b> <span class="pull-right">{{ auth()->user()->telepon }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Alamat</b> <span class="pull-right">{{ auth()->user()->alamat }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Role</b> <span class="pull-right">{{ auth()->user()->role }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="tab-pane" id="editprofil">
                    <form action="{{ route('admin.profil.update') }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Menambahkan method PUT untuk meniru PUT request -->
                        
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="{{ auth()->user()->username }}">
                                </div>
                    
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan password baru (jika ingin diganti)">
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Apakah anda yakin ingin memperbarui akun?')">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                                </button>
                                
                                @if(session('success'))
                                    <script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: '{{ session('success') }}',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    </script>
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    
                    
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
