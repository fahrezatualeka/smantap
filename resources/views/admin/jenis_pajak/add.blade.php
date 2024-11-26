@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        Jenis Pajak
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"> Tambah Data Jenis Pajak </h3>
            <div class="pull-right">
                <a href="{{ url('jenis_pajak') }}" class="btn btn-info">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form id="wpForm" action="{{ route('admin.jenis_pajak.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="jenispajak">Nama Jenis Pajak</label>
                            <input type="text" name="jenispajak" id="jenispajak" class="form-control" value="{{ old('jenispajak') }}">
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
