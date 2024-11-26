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
            <h3 class="box-title">Tambah Data Wajib Pajak</h3>
            <div class="pull-right">
                <a href="{{ url('data_piutang') }}" class="btn btn-info">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form id="wpForm" action="{{ route('admin.data_piutang.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="nama_pajak">Nama Pajak</label>
                            <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ old('nama_pajak') }}">
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control" value="{{ old('alamat') }}">
                        </div>
                        <div class="form-group">
                            <label for="npwpd">NPWPD</label>
                            <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ old('npwpd') }}">
                        </div>
                        {{-- <div class="form-group">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <input type="number" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}">
                        </div> --}}

                        <div class="form-group">
                            <label for="jenispajak">Jenis Pajak</label>
                            <select name="jenis_pajak_id" id="jenispajak" class="form-control">
                                <option value="">- Pilih -</option>
                                @foreach($jenisPajak as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->jenispajak }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kategoripajak">Kategori Pajak</label>
                            <select name="kategori_pajak_id" id="kategoripajak" class="form-control">
                                <option value="">- Pilih -</option>
                                @foreach($kategoriPajak as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->kategoripajak }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_tagihan">Tanggal Tagihan</label>
                            <input type="date" name="tanggal_tagihan" id="tanggal_tagihan" class="form-control" value="{{ old('tanggal_tagihan') }}">
                        </div>
                        <div class="form-group">
                            <label for="jumlah_piutang">Jumlah Piutang</label>
                            <input type="number" name="jumlah_piutang" id="jumlah_piutang" class="form-control" value="{{ old('jumlah_piutang') }}">
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
document.getElementById('wpForm').addEventListener('submit', function() {
var jumlahPiutangInput = document.getElementById('jumlah_piutang');
var value = jumlahPiutangInput.value.replace(/[^\d]/g, ''); // Hapus simbol dan titik
jumlahPiutangInput.value = value; // Set value hanya angka
});

// document.getElementById('jenispajak').addEventListener('change', function () {
//     const jenisPajakId = this.value;
//     const kategoriSelect = document.getElementById('kategoripajak');

//     // Reset dropdown kategori pajak
//     kategoriSelect.innerHTML = '<option value="">Pilih Kategori Pajak</option>';
//     kategoriSelect.setAttribute('disabled', true);

//     if (jenisPajakId) {
//         // Fetch kategori pajak berdasarkan jenis pajak ID
//         fetch(`/admin/data_wajibpajak/kategori/${jenisPajakId}`)
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error('Tidak ada kategori pajak untuk jenis pajak ini.');
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 if (data.length > 0) {
//                     kategoriSelect.removeAttribute('disabled');
//                     data.forEach(kategori => {
//                         const option = document.createElement('option');
//                         option.value = kategori.id;
//                         option.textContent = kategori.nama;
//                         kategoriSelect.appendChild(option);
//                     });
//                 } else {
//                     alert('Tidak ada kategori pajak untuk jenis pajak yang dipilih.');
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 alert('Terjadi kesalahan saat memuat kategori pajak.');
//             });
//     }
// });


</script>

@endsection