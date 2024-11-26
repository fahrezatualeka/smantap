@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 450px;
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Laporan Penagihan</b>
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
            timer: 2150
        });
    </script>
    @endif
    
    <div class="box-header with-border">
        <h3 class="box-title">Data Laporan Penagihan</h3>
    </div>

    <div class="box-body">
        <form action="{{ route('admin.laporan_penagihan.filter') }}" method="GET" id="filterForm">
            <div class="row">
                <!-- Filter Kategori Pajak -->
                <div class="col-md-3">
                    <label for="search">Pencarian:</label>
                    <input type="text" name="search" class="form-control" id="search" placeholder="- Semua -" value="{{ request()->search }}">
                </div>
    
                <!-- Filter Pembagian Zonasi -->
                <div class="col-md-2">
                    <label for="jenis_pajak_id">Jenis Pajak:</label>
                    <select name="jenis_pajak_id" class="form-control" id="jenis_pajak_id">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->jenis_pajak_id == '1' ? 'selected' : '' }}>Hotel</option>
                        <option value="2" {{ request()->jenis_pajak_id == '2' ? 'selected' : '' }}>Restoran</option>
                        <option value="3" {{ request()->jenis_pajak_id == '3' ? 'selected' : '' }}>Hiburan</option>
                    </select>
                    
                </div>

                {{-- <div class="col-md-2">
                    <label for="pembagian_zonasi">Kategori Pajak:</label>
                    <select name="jenis_pajak_id" class="form-control" id="pembagian_zonasi">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->jenis_pajak_id == '1' ? 'selected' : '' }}>Hotel</option>
                        <option value="2" {{ request()->jenis_pajak_id == '2' ? 'selected' : '' }}>Restoran</option>
                        <option value="3" {{ request()->jenis_pajak_id == '3' ? 'selected' : '' }}>Hiburan</option>
                    </select>
                </div> --}}

                <div class="col-md-2">
                    <label for="pembagian_zonasi">Pembagian Zonasi:</label>
                    <select name="pembagian_zonasi" class="form-control" id="pembagian_zonasi">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->pembagian_zonasi == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ request()->pembagian_zonasi == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ request()->pembagian_zonasi == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ request()->pembagian_zonasi == '4' ? 'selected' : '' }}>4</option>
                    </select>
                </div>
    
                <!-- Button Filter -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;"><i class="fa-solid fa-filter"></i> Filter Data</button>
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
                    <th>Nomor Telepon</th>
                    <th>Jenis Pajak</th>
                    <th>Kategori Pajak</th>
                    {{-- <th>Tanggal Tagihan</th> --}}
                    <th>Jumlah Piutang</th>
                    <th>Pembagian Zonasi</th>
                    <th style="width: 0px">Bukti Visit</th>
                    <th style="width: 0px">Bukti Pembayaran</th>
                    <th>Tanggal Pembayaran</th>
                    <th style="width: 0px">Status</th>
                    {{-- <th style="width: 100px">Verifikasi ke Wajib Pajak</th> --}}
                </tr>
            </thead>

            <tbody>
                @if($laporanPenagihan->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data laporan penagihan.</td>
                </tr>
                @else
                    @foreach ($laporanPenagihan as $key => $data)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $data->nama_pajak }}</td>
                        <td>{{ $data->alamat }}</td>
                        <td>{{ $data->npwpd }}</td>
                        <td>{{ $data->nomor_telepon }}</td>
                        <td>{{ $data->jenisPajak->jenispajak ?? 'N/A' }}</td>
                        <td>{{ $data->kategoriPajak->kategoripajak ?? 'N/A' }}</td>
                        {{-- <td>{{ $data->tanggal_tagihan }}</td> --}}
                        <td>Rp{{ number_format((float) $data->jumlah_piutang, 0, ',', '.') }}</td>

                        <td>{{ $data->pembagian_zonasi }}</td>
                        <td>
                            @if($data->uploadbuktivisit)
                            <a href="javascript:void(0);" 
                               class="btn btn-warning btn-xs" 
                               onclick="showImageProof('{{ route('admin.laporan_penagihan.showVisitProof', $data->id) }}', 'Bukti Visit')">
                                <i class="fa-solid fa-eye"></i> Lihat
                            </a>
                            @else
                                <span>Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            @if($data->uploadbuktipembayaran)
                            <a href="javascript:void(0);" 
                               class="btn btn-warning btn-xs" 
                               onclick="showImageProof('{{ route('admin.laporan_penagihan.showPaymentProof', $data->id) }}', 'Bukti Pembayaran')">
                                <i class="fa-solid fa-eye"></i> Lihat
                            </a>
                            @else
                                <span>Tidak ada</span>
                            @endif
                        </td>

                        <td>{{ $data->tanggal_pembayaran ? \Carbon\Carbon::parse($data->tanggal_pembayaran)->format('d-m-Y / H:i:s') : '' }}</td>
                        
                        
                        <td>{{ $data->status }}</td>
                        {{-- <td>
                            <a href="" 
                               class="btn btn-default btn-xs">
                               <i class="fa-solid fa-check"></i> Selesai
                            </a>
                        </td> --}}
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        <a href="" class="btn bg-black" target="_blank">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        {{-- <a href="" class="btn bg-black" target="_blank">
            <i class="fa-solid fa-file-pdf"></i> Export Pdf
        </a> --}}
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
</script>
    


@endsection