@extends('layout/template')

@section('content')
<section class="content-header">
    <h1>
        Kategori Pajak
    </h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Tambah Data Kategori Pajak</h3>
            <div class="pull-right">
                <a href="{{ url('kategori_pajak') }}" class="btn btn-info">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form action="{{ route('admin.kategori_pajak.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="jenis_pajak_id">Nama Jenis Pajak</label>
                            <select name="jenis_pajak_id" id="jenis_pajak_id" class="form-control">
                                <option value="">- Pilih -</option>
                                @foreach ($jenisPajaks as $jenisPajak)
                                    <option value="{{ $jenisPajak->id }}" {{ old('jenis_pajak_id') == $jenisPajak->id ? 'selected' : '' }}>
                                        {{ $jenisPajak->jenispajak }}  <!-- Nama jenis pajak -->
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        

                        <div class="form-group">
                            <label for="kategoripajak">Nama Kategori Pajak</label>
                            <input type="text" name="kategoripajak" id="kategoripajak" class="form-control" value="{{ old('kategoripajak') }}">
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
