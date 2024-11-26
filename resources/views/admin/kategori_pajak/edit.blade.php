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
            <h3 class="box-title"> Edit Data Kategori Pajak </h3>
            <div class="pull-right">
                <a href="{{ url('kategori_pajak') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form action="{{ route('admin.kategori_pajak.update', $kategoriPajak->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Dropdown untuk Jenis Pajak -->
                        <div class="form-group">
                            <label for="jenispajak_id">Nama Jenis Pajak</label>
                            <select name="jenispajak_id" id="jenispajak_id" class="form-control" required>
                                <option value="">Pilih Jenis Pajak</option>
                                @foreach ($jenisPajaks as $jenisPajak)
                                    <option value="{{ $jenisPajak->id }}" {{ $kategoriPajak->jenispajak_id == $jenisPajak->id ? 'selected' : '' }}>
                                        {{ $jenisPajak->jenispajak }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Input untuk Nama Kategori Pajak -->
                        <div class="form-group">
                            <label for="kategoripajak">Nama Kategori Pajak</label>
                            <input type="text" name="kategoripajak" id="kategoripajak" class="form-control" value="{{ old('kategoripajak', $kategoriPajak->kategoripajak) }}" required>
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