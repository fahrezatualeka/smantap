    @extends('layout/template')

    @section('content')
    <section class="content-header">
        <h1>
            Penetapan
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Tambah Data Penetapan</h3>
                <div class="pull-right">
                    <a href="{{ url('data_penetapan') }}" class="btn btn-info">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <form action="{{ route('admin.data_penetapan.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="npwpd">NPWPD</label>
                                <select name="npwpd" id="npwpd" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    @foreach ($dataWajibPajak as $wajibPajak)
                                        <option value="{{ $wajibPajak->npwpd }}">{{ $wajibPajak->npwpd }} - {{ $wajibPajak->nama_pajak }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_penagihan">Jumlah Penagihan</label>
                                <input type="number" name="jumlah_penagihan" id="jumlah_penagihan" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="bulan">Pilih Bulan</label>
                                <select name="bulan" id="bulan" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tahun">Pilih Tahun</label>
                                <select name="tahun" id="tahun" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-normal">
                                    <i class="fa fa-paper-plane"></i> Simpan
                                </button>
                                <button type="reset" class="btn bg-red btn-normal">
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