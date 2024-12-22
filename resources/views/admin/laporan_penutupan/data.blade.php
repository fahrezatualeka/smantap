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
        <b>Wajib Pajak Nonaktif</b>
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
            Laporan Wajib Pajak Nonaktif
        </h3>
    </div>

    
    <div class="box-body">
        <form action="{{ route('admin.laporan_penutupan.filter') }}" method="GET" id="filterForm">
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
                    {{-- <th>Jumlah Pembayaran</th> --}}
                    <th>Tanggal Kunjungan</th>
                    {{-- <th>Metode Pembayaran</th> --}}
                    {{-- <th>Jumlah Pembayaran</th> --}}
                    {{-- <th>Bukti Pembayaran</th> --}}
                    {{-- <th>Bukti SSPD</th> --}}
                    <th>Bukti Penutupan</th>
                    {{-- <th>Pengiriman</th> --}}
                    <th>Keterangan</th>
                    <th>Pengirim</th>
                </tr>
        </thead>
            <tbody>
                @if($laporanPenutupan->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada laporan wajib pajak nonaktif.</td>
                </tr>
                @else
                @foreach ($laporanPenutupan as $key => $penutupan)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $penutupan->nama_pajak }}</td>
                    <td>{{ $penutupan->alamat }}</td>
                    <td>{{ $penutupan->npwpd }}</td>
                    <td>{{ $penutupan->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    {{-- <td>{{ $pelunasan->kategoriPajak->kategoripajak ?? 'N/A' }}</td>                     --}}
                    <td>{{ $penutupan->telepon }}</td>
                    <td>{{ $penutupan->zona }}</td>
                    {{-- <td>Rp{{ number_format((float) $pelunasan->tagihan, 0, ',', '.') }}</td> --}}
                    <td>{{ $penutupan->periode }}</td>

                    <td>{{ \Carbon\Carbon::parse($penutupan->tanggal_kunjungan)->locale('id')->isoFormat('D MMMM YYYY') }}</td>

                    {{-- <td>{{ $penutupan->metode_pembayaran }}</td> --}}
                    {{-- <td>{{ $penutupan->jumlah_pembayaran }}</td> --}}
                    

                    <td>
                        @if($penutupan->buktipenutupan)
                        <a href="javascript:void(0);" 
                        class="btn btn-warning btn-xs" 
                        onclick="showImageProof('{{ route('admin.laporan_penutupan.showPenutupanProof', $penutupan->id) }}', 'Bukti Penutupan')">
                        <i class="fa-solid fa-eye"></i> Lihat
                    </a>
                    {{-- <a href="{{ Storage::url($penutupan->buktivisit) }}" 
                        target="_blank" 
                        class="btn btn-warning btn-xs">
                        <i class="fa-solid fa-eye"></i> Lihat
                     </a> --}}
                    @else
                    <span>Tidak ada</span>
                    @endif
                </td>

            {{-- <td>{{ $pelunasan->tempat_pembayaran }}</td> --}}
            {{-- <td>{{ $penutupan->keterangan }}</td> --}}
            <td>{{ $penutupan->keterangan ?? 'Tidak ada' }}</td>
            <td>{{ $penutupan->pengirim ?? 'Tidak ada' }}</td>



                </tr>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="{{ route('admin.laporan_penutupan.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        
        <a href="{{ route('admin.laporan_penutupan.exportPdf', [
            'search' => request('search'),
            'jenis_pajak_id' => request('jenis_pajak_id'),
            'zona' => request('zona'),
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
</script>

@endsection