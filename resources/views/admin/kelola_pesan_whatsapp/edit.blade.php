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
            <h3 class="box-title"> Edit Data Kelola Pesan WhatsApp </h3>
            <div class="pull-right">
                <a href="{{ url('kelola_pesan_whatsapp') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">

                    <form id="kelolapesanwhatsappForm" action="{{ route('admin.kelola_pesan_whatsapp.update', $datawhatsapp->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <label for="jenis_pesan">Jenis Pesan</label>
                            <input type="text" name="jenis_pesan" id="jenis_pesan" class="form-control" value="{{ old('jenis_pesan', $datawhatsapp->jenis_pesan) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <input type="text" name="deskripsi" id="deskripsi" class="form-control" value="{{ old('deskripsi', $datawhatsapp->deskripsi) }}" required>
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
