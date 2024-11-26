@extends('layout/template')

@section('content')
    <section class="content-header">
        <h1>
            Wajib Pajak
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"> Edit Data Wajib Pajak </h3>
                <div class="pull-right">
                    <a href="{{ url('data_wajibpajak') }}" class="btn btn-info btn-normal">
                        <i class="fa fa-arrow-left"></i> Kembali </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <form id="datawajibpajakForm" action="{{ route('admin.data_wajibpajak.update', $dataWajibPajak->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            
                            <div class="form-group">
                                <label for="nama_pajak">Nama Pajak</label>
                                <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $dataWajibPajak->nama_pajak }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" id="alamat" class="form-control" value="{{ $dataWajibPajak->alamat }}">
                            </div>

                            <div class="form-group">
                                <label for="npwpd">NPWPD</label>
                                <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $dataWajibPajak->npwpd }}">
                            </div>

                            {{-- <div class="form-group">
                                <label for="nomor_telepon">Nomor Telepon</label>
                                <input type="number" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ $dataWajibPajak->nomor_telepon }}">
                            </div> --}}

                            <div class="form-group">
                                <label for="jenis_pajak_id">Jenis Pajak</label>
                                <select name="jenis_pajak_id" id="jenis_pajak_id" class="form-control">
                                    @foreach($jenisPajak as $jenis)
                                        <option value="{{ $jenis->id }}" {{ $jenis->id == $dataWajibPajak->jenis_pajak_id ? 'selected' : '' }}>
                                            {{ $jenis->jenispajak }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="kategori_pajak_id">Kategori Pajak</label>
                                <select name="kategori_pajak_id" id="kategori_pajak_id" class="form-control">
                                    @foreach($kategoriPajak as $kategori)
                                        <option value="{{ $kategori->id }}" {{ $kategori->id == $dataWajibPajak->kategori_pajak_id ? 'selected' : '' }}>
                                            {{ $kategori->kategoripajak }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label for="tanggal_tagihan">Tanggal Tagihan</label>
                                <input type="date" name="tanggal_tagihan" id="tanggal_tagihan" class="form-control" value="{{ $dataWajibPajak->tanggal_tagihan }}">
                            </div> --}}

                            {{-- <div class="form-group">
                                <label for="jumlah_piutang">Jumlah Piutang</label>
                                <input type="text" name="jumlah_piutang" id="jumlah_piutang" class="form-control" 
                                       value="{{ $dataWajibPajak->jumlah_piutang > 0 ? number_format($dataWajibPajak->jumlah_piutang, 0, ',', '.') : '' }}">
                            </div> --}}
                            
                            

                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-normal">
                                    <i class="fa fa-paper-plane"></i> Simpan
                                </button>
                                <button type="reset" class="btn bg-red btn-normal">
                                    <i class="fa fa-refresh"></i> Ulangi
                                </button>
                            </div>

                        </form>

                            @if(session('success'))
                                <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: '{{ session('success') }}'
                                    });
                                </script>
                            @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.getElementById('jumlah_piutang').addEventListener('input', function (e) {
    var value = e.target.value.replace(/[^\d]/g, ''); // Hapus semua karakter non-digit
    if (value) {
        // Menambahkan simbol Rp dan titik pemisah ribuan
        e.target.value = 'Rp' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    } else {
        e.target.value = ''; // Jika kosong, hilangkan simbol Rp
    }
});

// Menghapus simbol 'Rp' dan titik saat form disubmit
document.getElementById('datawajibpajakForm').addEventListener('submit', function() {
    var jumlahPiutangInput = document.getElementById('jumlah_piutang');
    var value = jumlahPiutangInput.value.replace(/[^\d]/g, ''); // Hapus simbol dan titik
    jumlahPiutangInput.value = value; // Set value hanya angka
});

    </script>
@endsection
