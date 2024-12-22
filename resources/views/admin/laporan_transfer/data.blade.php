@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 570px;

        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Transfer</b>
    </h1>
</section>
<section class="content">
<div class="box">

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2250
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
            timer: 2250
        });
    </script>
@endif


    <div class="box-header with-border">
        <h3 class="box-title">
            Laporan Transfer
        </h3>
    </div>

    <div class="box-body">
        <form action="{{ route('admin.laporan_transfer.filter') }}" method="GET" id="filterForm">
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


                <div class="col-md-2">
                    <label for="zona">Zona:</label>
                    <select name="zona" class="form-control" id="zona">
                        <option value="">- Semua -</option>
                        @foreach(range(1, 4) as $zona)
                            <option value="{{ $zona }}" {{ request('zona') == $zona ? 'selected' : '' }}>
                                {{ $zona }}
                            </option>
                        @endforeach
                    </select>
                </div>
    
                <div class="col-md-2">
                    <label for="bulan">Periode Piutang Pajak:</label>
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="">- Semua -</option>
                        <option value="January" {{ request()->bulan == 'January' ? 'selected' : '' }}>Januari</option>
                        <option value="February" {{ request()->bulan == 'February' ? 'selected' : '' }}>Februari</option>
                        <option value="March" {{ request()->bulan == 'March' ? 'selected' : '' }}>Maret</option>
                        <option value="April" {{ request()->bulan == 'April' ? 'selected' : '' }}>April</option>
                        <option value="May" {{ request()->bulan == 'May' ? 'selected' : '' }}>Mei</option>
                        <option value="June" {{ request()->bulan == 'June' ? 'selected' : '' }}>Juni</option>
                        <option value="July" {{ request()->bulan == 'July' ? 'selected' : '' }}>Juli</option>
                        <option value="August" {{ request()->bulan == 'August' ? 'selected' : '' }}>Agustus</option>
                        <option value="September" {{ request()->bulan == 'September' ? 'selected' : '' }}>September</option>
                        <option value="October" {{ request()->bulan == 'October' ? 'selected' : '' }}>Oktober</option>
                        <option value="November" {{ request()->bulan == 'November' ? 'selected' : '' }}>November</option>
                        <option value="December" {{ request()->bulan == 'December' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="konfirmasi">Status Konfirmasi ke WP:</label>
                    <select name="konfirmasi" class="form-control" id="konfirmasi">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->konfirmasi == '1' ? 'selected' : '' }}>Belum Kirim</option>
                        <option value="2" {{ request()->konfirmasi == '2' ? 'selected' : '' }}>Sudah Kirim</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    

    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped" id="table1">
        <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pajak</th>
                    <th>Alamat</th>
                    <th>NPWPD</th>
                    <th>Jenis Pajak</th>
                    {{-- <th>Kategori Pajak</th> --}}
                    <th>Telepon</th>
                    <th>Zona</th>
                    {{-- <th>Tagihan</th> --}}
                    <th>Periode</th>
                    <th>Tanggal Pembayaran</th>
                    {{-- <th>Metode Pembayaran</th> --}}
                    <th>Jumlah Pembayaran</th>
                    <th>Bukti Pembayaran</th>
                    <th>Bukti SSPD</th>
                    <th>Keterangan</th>
                    <th>Pengirim</th>
                    <th>Konfirmasi ke WP</th>
                </tr>
        </thead>
            <tbody>
                @if($laporanTransfer->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada laporan transfer.</td>
                </tr>
                @else
                @foreach ($laporanTransfer as $key => $transfer)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $transfer->nama_pajak }}</td>
                    <td>{{ $transfer->alamat }}</td>
                    <td>{{ $transfer->npwpd }}</td>
                    <td>{{ $transfer->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    {{-- <td>{{ $pelunasan->kategoriPajak->kategoripajak ?? 'N/A' }}</td>                     --}}
                    <td>{{ $transfer->telepon }}</td>
                    <td>{{ $transfer->zona }}</td>
                    {{-- <td>Rp{{ number_format((float) $pelunasan->tagihan, 0, ',', '.') }}</td> --}}
                    <td>{{ $transfer->periode }}</td>
                    <td>{{ \Carbon\Carbon::parse($transfer->tanggal_pembayaran)->locale('id')->isoFormat('D MMMM YYYY') }}</td>

                    {{-- <td>{{ $transfer->metode_pembayaran }}</td> --}}
                    <td>Rp{{ number_format((float) $transfer->jumlah_pembayaran, 0, ',', '.') }}</td>

                    
                <td>
                    @if($transfer->buktipembayaran)
                    <a href="javascript:void(0);" 
                    class="btn btn-warning btn-xs" 
                    onclick="showImageProof('{{ route('admin.laporan_transfer.showPaymentProof', $transfer->id) }}', 'Bukti Pembayaran')">
                    <i class="fa-solid fa-eye"></i> Lihat
                </a>
                @else
                <span>Tidak ada</span>
                @endif
            </td>

                <td>
                    @if($transfer->buktisspd)
                    <a href="javascript:void(0);" 
                    class="btn btn-warning btn-xs" 
                    onclick="showImageProof('{{ route('admin.laporan_transfer.showSspdProof', $transfer->id) }}', 'Bukti SSPD')">
                    <i class="fa-solid fa-eye"></i> Lihat
                </a>
                @else
                <span>Tidak ada</span>
                @endif
            </td>
            

            <td>{{ $transfer->keterangan ?? 'Tidak ada' }}</td>

            <td>{{ $transfer->pengirim }}</td>

            <td id="konfirmasi-{{ $transfer->id }}">
                @if(empty($transfer->buktisspd))
                    <span>Belum ada SSPD</span>
                @else
                    @if($transfer->konfirmasi === 'Belum kirim')
                        <button class="btn btn-default btn-xs btn-kirim" data-id="{{ $transfer->id }}">
                            Kirim Pesan
                        </button>
                        <div id="loading-{{ $transfer->id }}" class="spinner-border text-info" style="display: none; width: 1.5rem; height: 1.5rem;"></div>
                    @else
                        <span>Sudah Kirim</span>
                    @endif
                @endif
            </td>


                </tr>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="{{ route('admin.laporan_transfer.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.laporan_transfer.exportPdf', [
    'search' => request('search'),
    'jenis_pajak_id' => request('jenis_pajak_id'),
    'zona' => request('zona'),
    'konfirmasi' => request('konfirmasi'),
    'bulan' => request('bulan'),
]) }}" class="btn bg-black">
    <i class="fa-solid fa-file-pdf"></i> Export PDF
</a>
    </div>
</div>
</section>

<script>
    function showImageProof(url, title) {
        Swal.fire({
            title: title,
            imageUrl: url,  // Load the image directly from the URL
            imageAlt: title,
            imageWidth: 500,
            imageHeight: 500,
            showCloseButton: true,
            showConfirmButton: false,  // Remove confirm button to just show the image
            width: 'auto',
            padding: '1em',
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filterForm");

    // Trigger form submit on input or select change
    form.querySelectorAll("input, select").forEach(element => {
        element.addEventListener("change", function () {
            form.submit();
        });

        // Untuk input teks, deteksi ketika pengguna berhenti mengetik
        if (element.type === "text") {
            let typingTimer;
            element.addEventListener("keyup", function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => form.submit(), 500); // Submit setelah 500ms berhenti mengetik
            });
        }
    });
});

// TOMBOL STATUS
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-kirim');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const loadingSpinner = document.getElementById(`loading-${id}`);  // Spinner untuk loading
            const konfirmasiCell = document.getElementById(`konfirmasi-${id}`);  // Cell konfirmasi untuk update status

            // Menampilkan spinner dan menyembunyikan tombol kirim
            loadingSpinner.style.display = 'inline-block';
            this.disabled = true;

            // Menampilkan konfirmasi peringatan dengan SweetAlert2
            Swal.fire({
                title: 'Peringatan',
                text: 'Apakah Anda yakin bahwa data SSPD yang dikirim sudah benar? Pastikan semua informasi telah diperiksa dengan teliti.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Melanjutkan dengan pengiriman setelah konfirmasi
                    fetch(`/laporan-transfer/konfirmasi/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Ubah status menjadi "Sudah Kirim"
                            konfirmasiCell.innerHTML = '<span>Sudah Kirim</span>';

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Status berhasil diperbarui.',
                                showConfirmButton: false,
                                timer: 2000,
                            });
                        } else {
                            // Jika gagal, tampilkan error detail
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.error_detail || 'Pesan gagal dikirim.',
                                showConfirmButton: true,
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat mengirimkan pesan.',
                            showConfirmButton: true,
                        });
                    })
                    .finally(() => {
                        // Sembunyikan spinner dan aktifkan kembali tombol
                        loadingSpinner.style.display = 'none';
                        button.disabled = false;
                    });
                } else {
                    // Jika dibatalkan, hanya sembunyikan spinner dan aktifkan kembali tombol
                    loadingSpinner.style.display = 'none';
                    button.disabled = false;
                }
            });
        });
    });
});

function kirimKonfirmasi(id) {
    if (!confirm('Apakah anda yakin dengan data ini?')) return;

    fetch(`/update-konfirmasi/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({}),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                document.querySelector(`#konfirmasi-${id}`).innerHTML = '<span>Sudah Kirim</span>';
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>

@endsection