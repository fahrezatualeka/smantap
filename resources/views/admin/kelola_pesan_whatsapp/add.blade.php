@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        Kelola Pesan WhatsApp
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"> Tambah Data Kelola Pesan WhatsApp </h3>
            <div class="pull-right">
                <a href="{{ url('kelola_pesan_whatsapp') }}" class="btn btn-info">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form id="wpForm" action="{{ route('admin.kelola_pesan_whatsapp.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="jenis_pesan">Jenis Pesan</label>
                            <select name="jenis_pesan" required>
                                <option value="Pengiriman Pesan dari Petugas Penagihan" {{ old('jenis_pesan') == 'Pengiriman Pesan dari Petugas Penagihan' ? 'selected' : '' }}>Pengiriman Pesan dari Petugas Penagihan</option>
                                <option value="Pengiriman Pesan dari Admin" {{ old('jenis_pesan') == 'Pengiriman Pesan dari Admin' ? 'selected' : '' }}>Pengiriman Pesan dari Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <input type="text" name="deskripsi" id="deskripsi" class="form-control" value="{{ old('deskripsi') }}">
                        </div>
                        

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

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
