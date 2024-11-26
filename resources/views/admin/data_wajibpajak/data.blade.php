@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        /* max-height: 435px; */
        max-height: 375px;
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Wajib Pajak</b>
    </h1>
</section>
<section class="content">

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000 // Otomatis hilang setelah 2 detik
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif


<div class="box">

    <div class="box-header with-border">
        <h3 class="box-title"> Data Wajib Pajak</h3>
    </div>
        
        <div class="box-body table-responsive">
            <form action="{{ route('admin.data_wajibpajak.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="import_data_wajibpajak"><i class="fa-solid fa-file-import"></i> Import Data Wajib Pajak</label>
                    <input type="file" name="import_data_wajibpajak" id="import_data_wajibpajak" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan data ini?')">
                    <i class="fa fa-paper-plane"></i> Kirim
                </button>
                <div class="pull-right">
                    <a href="{{ route('admin.data_wajibpajak.create') }}" class="btn btn-primary">
                        <i class="fa fa-user-plus"></i> Tambah Data
                    </a>
                </div>
            </form>
            
        </div>

    
    {{-- <div class="box-body">
        <form action="{{ route('admin.data_wajibpajak.filter') }}" method="GET" id="filterForm">
            <div class="row">
                <div class="col-md-2">
                    <label for="search">Pencarian:</label>
                    <input type="text" name="search" class="form-control" id="search" placeholder="- Semua -" value="{{ request()->search }}">
                </div>
    
                <!-- Filter Pembagian Zonasi -->
                <div class="col-md-2">
                    <label for="pembagian_zonasi">Pembagian Zonasi:</label>
                    <select name="pembagian_zonasi" class="form-control" id="pembagian_zonasi">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->pembagian_zonasi == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ request()->pembagian_zonasi == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ request()->pembagian_zonasi == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ request()->pembagian_zonasi == '4' ? 'selected' : '' }}>4</option>
                        <option value="5" {{ request()->pembagian_zonasi == '5' ? 'selected' : '' }}>5</option>
                    </select>
                </div>
    
                <!-- Button Filter -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;"><i class="fa-solid fa-filter"></i> Filter Data</button>
                </div>
            </div>
        </form>
    </div> --}}

    <div class="box-body">
        <form action="{{ route('admin.data_wajibpajak.filter') }}" method="GET" id="filterForm">
            <div class="row">
                <!-- Filter Pencarian -->
                <div class="col-md-3">
                    <label for="search">Pencarian:</label>
                    <input type="text" name="search" class="form-control" id="search" placeholder="- Semua -" value="{{ request()->search }}">
                </div>
    
                <!-- Filter Jenis Pajak -->
                <div class="col-md-2">
                    <label for="jenis_pajak_id">Jenis Pajak:</label>
                    <select name="jenis_pajak_id" class="form-control" id="jenis_pajak_id">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->jenis_pajak_id == '1' ? 'selected' : '' }}>Hotel</option>
                        <option value="2" {{ request()->jenis_pajak_id == '2' ? 'selected' : '' }}>Restoran</option>
                        <option value="3" {{ request()->jenis_pajak_id == '3' ? 'selected' : '' }}>Hiburan</option>
                    </select>
                </div>
    
                <!-- Filter Kategori Pajak -->
                {{-- <div class="col-md-2">
                    <label for="kategori_pajak_id">Kategori Pajak:</label>
                    <select name="kategori_pajak_id" class="form-control" id="kategori_pajak_id">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->kategori_pajak_id == '1' ? 'selected' : '' }}>Kategori 1</option>
                        <option value="2" {{ request()->kategori_pajak_id == '2' ? 'selected' : '' }}>Kategori 2</option>
                        <option value="3" {{ request()->kategori_pajak_id == '3' ? 'selected' : '' }}>Kategori 3</option>
                    </select>
                </div> --}}
    
                <!-- Button Filter -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;">
                        <i class="fa-solid fa-filter"></i> Filter Data
                    </button>
                </div>
            </div>
        </form>
    </div>
    

    <div class="box-body table-responsive">
        {{-- <form action="{{ route('admin.data_wajibpajak.zonasi') }}" method="POST">
            @csrf --}}
            {{-- <form action="{{ route('admin.data_wajibpajak.updateTagihanPiutang') }}" method="POST" id="form-update">
                @csrf --}}
            <table class="table table-bordered table-striped" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pajak</th>
                        <th>Alamat</th>
                        <th>NPWPD</th>
                        {{-- <th>Nomor Telepon</th> --}}
                        <th>Jenis Pajak</th>
                        <th>Kategori Pajak</th>
                        {{-- <th>Tanggal Tagihan</th> --}}
                        {{-- <th>Jumlah Piutang</th> --}}
                        <th style="width: 124px">Aksi</th>
                        {{-- <th style="width: 0px">Aksi</th> --}}
                        {{-- <th style="width: 0px">Pelunasan</th> --}}
                        {{-- <th>wa</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @if($data->isEmpty())
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data wajib pajak.</td>
                    </tr>
                    @else
                    @foreach ($data as $key => $datawajibpajak)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $datawajibpajak->nama_pajak }}</td>
                        <td>{{ $datawajibpajak->alamat }}</td>
                        <td>{{ $datawajibpajak->npwpd }}</td>
                        {{-- <td>{{ $datawajibpajak->nomor_telepon }}</td> --}}
                        <td>{{ $datawajibpajak->jenisPajak ? $datawajibpajak->jenisPajak->jenispajak : 'Tidak Ditemukan' }}</td>
                        <td>{{ $datawajibpajak->kategoriPajak ? $datawajibpajak->kategoriPajak->kategoripajak : 'Tidak Ditemukan' }}</td>
                        {{-- <td>Rp{{ number_format((float) $datawajibpajak->jumlah_piutang, 0, ',', '.') }}</td> --}}
                        {{-- <td>
                            @if($datawajibpajak->jumlah_piutang > 0)
                                Rp{{ number_format((float) $datawajibpajak->jumlah_piutang, 0, ',', '.') }}
                            @else
                                Rp
                            @endif
                        </td> --}}
                        



                        
                        
                        {{-- <td>
                            @if ($datawajibpajak->is_readonly)
                                <span>{{ $datawajibpajak->tanggal_tagihan }}</span>
                            @else
                                <input type="date" name="tanggal_tagihan[{{ $datawajibpajak->id }}]" value="{{ $datawajibpajak->tanggal_tagihan }}" class="form-control">
                            @endif
                        </td> --}}
                        {{-- <td>
                            @if ($datawajibpajak->is_readonly)
                                <span>Rp {{ number_format($datawajibpajak->jumlah_piutang, 0, ',', '.') }}</span>
                            @else
                                <input type="text" name="jumlah_piutang[{{ $datawajibpajak->id }}]" 
                                       value="{{ $datawajibpajak->jumlah_piutang ? number_format($datawajibpajak->jumlah_piutang, 0, ',', '.') : '' }}" 
                                       class="form-control" 
                                       id="jumlah_piutang_{{ $datawajibpajak->id }}" 
                                       oninput="formatCurrency(this)" 
                                       onblur="formatCurrency(this)">
                            @endif
                        </td> --}}
    
                        {{-- <td>
                            @if(is_null($datawajibpajak->pembagian_zonasi))
                            <select name="zonasi[{{ $datawajibpajak->id }}]" class="form-control">
                                <option value="">- Pilih -</option>
                                @for ($i = 1; $i <= 4; $i++)
                                <option value="{{ $i }}" {{ old('zonasi.' . $datawajibpajak->id) == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                                @endfor
                            </select>
                            @else
                            <p>{{ $datawajibpajak->pembagian_zonasi }}</p>
                            @endif
                        </td> --}}
                        {{-- <td>
                            @if($datawajibpajak->status_lunas == 'Lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <button class="btn btn-success btn-xs" onclick="markAsLunas({{ $datawajibpajak->id }})">
                                    <i class="fa-solid fa-check"></i> Terbayar
                                </button>
                            @endif
                        </td> --}}
                        
                        <td>
                            <a href="{{ url('data_wajibpajak/edit/'.$datawajibpajak->id) }}" class="btn bg-green btn-xs">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <form action="{{ url('data_wajibpajak/delete/'.$datawajibpajak->id) }}" method="post" style="display: inline-block;">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin ingin menghapus data? karena data yang anda hapus akan otomatis terhapus di data penetapan.')">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </form>
                            
                            
                        </td>
                        {{-- <td>
                            @if($datawajibpajak->status_lunas == 'Lunas')
                                Lunas
                            @else
                                <button class="btn btn-default btn-xs" onclick="markAsLunas({{ $datawajibpajak->id }})">
                                    <i class="fa fa-dollar"></i> Terbayar
                                </button>
                            @endif
                        </td> --}}

                        {{-- <td>
                            @if ($datawajibpajak->nomor_telepon)
                                <form action="{{ route('data-wajib-pajak.send-whatsapp') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="to" value="{{ $datawajibpajak->nomor_telepon }}">
                                    <input type="hidden" name="message" value="Halo {{ $datawajibpajak->nama_pajak }}, ini adalah pesan otomatis dari sistem kami.">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Kirim WhatsApp
                                    </button>
                                </form>
                            @else
                                <span class="text-danger">Nomor Tidak Tersedia</span>
                            @endif
                        </td> --}}
                        
                        
                        
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </form>
    </div>
    
    <div class="box-footer text-left">
        <a href="{{ route('admin.data_wajibpajak.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.data_wajibpajak.exportPdf') }}" class="btn bg-black">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>
</section>

<script>

$('input[name="jumlah_piutang"]').on('blur', function () {
    let rawValue = $(this).val().replace(/,/g, '');  // Menghapus koma
    $(this).val(rawValue);
});


// Format currency sebelum mengirimkan ke server
function formatCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');  // Menghapus semua non-numerik
    input.value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Menangani saat form input blur
$('input[name^="jumlah_piutang"]').on('blur', function () {
    let rawValue = $(this).val().replace(/,/g, '');  // Menghapus koma
    $(this).val(rawValue);
});




// tanggalTagihan
// $(document).on('submit', '#form-update', function (e) {
//     e.preventDefault(); // Mencegah pengiriman form secara default

//     var form = $(this);
//     var formData = new FormData(this);

//     // Log data untuk memeriksa nilai yang dikirim
//     for (var pair of formData.entries()) {
//         console.log(pair[0]+ ': ' + pair[1]); 
//     }

//     $.ajax({
//         url: form.attr('action'), // Mengambil URL dari action form
//         method: 'POST',
//         data: formData,
//         processData: false,
//         contentType: false,
//         success: function (response) {
//             if (response.success) {
//                 Swal.fire('Berhasil!', response.message, 'success');
//                 // Setelah berhasil disimpan, ubah input menjadi readonly
//                 $('input[name^="tanggal_tagihan"]').attr('readonly', true);
//                 $('input[name^="jumlah_piutang"]').attr('readonly', true);
//             } else {
//                 Swal.fire('Gagal!', response.message, 'error');
//             }
//         },
//         error: function (xhr, status, error) {
//             console.log("Error response:", xhr.responseText);  // Menampilkan error response
//             Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data', 'error');
//         }
//     });
// });



// LIHAT DATA
// function lihatDetail(npwpd) {
//         fetch(`/data-wajibpajak/${npwpd}`)
//             .then(response => response.json())
//             .then(data => {
//                 Swal.fire({
//                     title: 'Detail Data Wajib Pajak',
//                     html: `
//                         <div style="font-size: 14px; text-align: left;">
//                             <b>Nama Pajak:</b> ${data.nama_pajak} <br>
//                             <b>Alamat:</b> ${data.alamat} <br>
//                             <b>NPWPD:</b> ${data.npwpd} <br>
//                             <b>Nomor Telepon:</b> ${data.nomor_telepon} <br>
//                             <b>Jenis Pajak:</b> ${data.jenis_pajak_id} <br>
//                             <b>Kategori Pajak:</b> ${data.kategori_pajak_id} <br>
//                             <b>Tanggal Tagihan:</b> ${new Date(data.tanggal_tagihan).toLocaleDateString()} <br>
//                             <b>Jumlah Piutang:</b> Rp${parseInt(data.jumlah_piutang).toLocaleString('id-ID')} <br>
//                         </div>
//                     `,
//                     showConfirmButton: false
//                 });
//             })
//             .catch(error => {
//                 console.error('Error fetching data:', error);
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Oops!',
//                     text: 'Gagal mengambil data.',
//                     showConfirmButton: false
//                 });
//             });
//     }




// tombol lunas
function markAsLunas(id) {
    if (confirm('Apakah Anda yakin data ini sudah lunas?')) {
        // Kirim permintaan ke server untuk mengubah status menjadi Lunas
        axios.post('{{ route('admin.data_wajibpajak.mark_as_lunas') }}', { id: id })
            .then(response => {
                if (response.data.success) {
                    Swal.fire('Berhasil!', response.data.message, 'success')
                        .then(() => {
                            location.reload(); // Muat ulang halaman setelah perubahan
                        });
                } else {
                    Swal.fire('Gagal!', response.data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
            });
    }
}



</script>
    
    

@endsection