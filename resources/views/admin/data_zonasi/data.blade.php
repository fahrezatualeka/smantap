@extends('layout/template')
@section('content')

@php
function getBulanIndonesia($bulan)
{
    $namaBulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember',
    ];

    return $namaBulan[$bulan] ?? $bulan;
}
@endphp


<style>
    .table-responsive {
        max-height: 450px;
        overflow-y: auto;
        }
</style>

<section class="content-header">
    <h1>
        <b>Piutang</b>
    </h1>
</section>

<section class="content">
<div class="box">

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 5000
        });
    </script>
    @endif
    
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 5000
        });
    </script>
    @endif
    


    <div class="box-header with-border">
        <h3 class="box-title">Data Piutang</h3>
    </div>
        <div class="box-body table-responsive">
            <form action="{{ route('admin.data_zonasi.import') }}" method="GET" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="bulan">Pilih Bulan</label>
                    <select name="bulan" id="bulan" class="form-control" required>
                        <option value="">- Pilih -</option>
                        <option value="January">Januari</option>
                        <option value="February">Februari</option>
                        <option value="March">Maret</option>
                        <option value="April">April</option>
                        <option value="May">Mei</option>
                        <option value="June">Juni</option>
                        <option value="July">Juli</option>
                        <option value="August">Agustus</option>
                        <option value="September">September</option>
                        <option value="October">Oktober</option>
                        <option value="November">November</option>
                        <option value="December">Desember</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" id="importButton" onclick="return confirm('Apakah anda yakin dengan pemilihan bulan ini?')">
                    <i class="fa-solid fa-file-import"></i> Import Piutang dari Data Wajib Pajak
                </button>
            </form>
            
            
            
        </div>        

    <div class="box-body">
        <form action="{{ route('admin.data_zonasi.filter') }}" method="GET" id="filterForm">
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

                {{-- <div class="col-md-2">
                    <label for="pembagian_zonasi">Pembagian Zonasi:</label>
                    <select name="pembagian_zonasi" class="form-control" id="pembagian_zonasi">
                        <option value="">- Semua -</option>
                        <option value="1" {{ request()->pembagian_zonasi == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ request()->pembagian_zonasi == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ request()->pembagian_zonasi == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ request()->pembagian_zonasi == '4' ? 'selected' : '' }}>4</option>
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
        <form action="{{ route('admin.data_zonasi.store') }}" method="POST">
            @csrf
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
                        <th>Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->isEmpty())
                    <tr>
                        <td colspan="11" class="text-center">Tidak ada data zonasi.</td>
                    </tr>
                    @else
                    @foreach ($data as $key => $datazonasi)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $datazonasi->nama_pajak }}</td>
                        <td>{{ $datazonasi->alamat }}</td>
                        <td>{{ $datazonasi->npwpd }}</td>
                        <td>{{ $datazonasi->nomor_telepon }}</td>
                        <td>{{ $datazonasi->jenisPajak->jenispajak ?? 'N/A' }}</td>
                        <td>{{ $datazonasi->kategoriPajak->kategoripajak ?? 'N/A' }}</td>
                        {{-- <td>{{ $datazonasi->tanggal_tagihan }}</td> --}}
                        {{-- <td>Rp{{ number_format((float) $datazonasi->jumlah_piutang, 0, ',', '.') }}</td> --}}

                        <td>
                            @if($datazonasi->jumlah_piutang)
                                <!-- Jika jumlah piutang sudah ada, tampilkan sebagai teks biasa -->
                                <span>Rp{{ number_format((float) $datazonasi->jumlah_piutang, 0, ',', '.') }}</span>
                            @else
                                <!-- Jika jumlah piutang belum ada, tampilkan sebagai input number -->
                                <input 
                                    type="number" 
                                    name="jumlah_piutang[{{ $datazonasi->id }}]" 
                                    class="form-control" 
                                    step="0.01" 
                                    placeholder="Rp0"
                                    value="{{ old('jumlah_piutang.' . $datazonasi->id, $datazonasi->jumlah_piutang) }}" 
                                >
                            @endif
                        </td>
                        
                        
                        

                        <td>
                            @if(is_null($datazonasi->pembagian_zonasi))
                                <select name="zonasi[{{ $datazonasi->id }}]" class="form-control">
                                    <option value="">Pilih Pembagian Zonasi</option>
                                    @for ($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ $datazonasi->pembagian_zonasi == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                    @endfor
                                </select>
                            @else
                                <p>{{ $datazonasi->pembagian_zonasi }}</p>
                            @endif
                        </td>
                        {{-- <td>{{ \Carbon\Carbon::parse($datazonasi->bulan)->format('m') }}</td> --}}
                        
                        <td>{{ getBulanIndonesia($datazonasi->bulan) }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
            {{-- @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
            @endif --}}
        </div>
        
        <div class="box-footer text-left">
            <a href="" class="btn bg-black" target="_blank">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        <div class="pull-right">
            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan pembagian zonasi tersebut?')"><i class="fa-solid fa-floppy-disk"></i> Simpan Zonasi</button>
        </div>
        </div>
    </form>
</div>
    
</section>

<script>

    
    document.addEventListener('DOMContentLoaded', function () {
        // Setelah tombol simpan diklik
        const saveButton = document.querySelector('button[type="submit"]');
        
        saveButton.addEventListener('click', function () {
            // Menunggu form untuk dikirim
            const inputs = document.querySelectorAll('input[name^="jumlah_piutang"]');
            
            inputs.forEach(function(input) {
                const value = input.value;
                if (value !== "") {
                    // Ganti input dengan nilai sebagai teks
                    const span = document.createElement('span');
                    span.innerHTML = `Rp${new Intl.NumberFormat('id-ID').format(value)}`;
                    input.replaceWith(span);
                }
            });
        });
    });
</script>



@endsection