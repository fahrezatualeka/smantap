@extends('layout/template')

@section('content')
    <section class="content-header">
        <h1>Penetapan</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Edit Data Penetapan</h3>
                <div class="pull-right">
                    <a href="{{ url('data_penetapan') }}" class="btn btn-info btn-normal">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <form id="datawajibpajakForm" action="{{ route('admin.data_penetapan.update', $dataPenetapan->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            
                            <div class="form-group">
                                <label for="nama_pajak">Nama Pajak</label>
                                <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $dataPenetapan->nama_pajak }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="npwpd">NPWPD</label>
                                <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $dataPenetapan->npwpd }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="jumlah_penagihan">Jumlah Penagihan</label>
                                <input type="text" name="jumlah_penagihan" id="jumlah_penagihan" class="form-control" 
                                       value="{{ number_format($dataPenetapan->jumlah_penagihan, 0, '', '') }}" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="bulan">Pilih Bulan</label>
                                <select name="bulan" id="bulan" class="form-control" required>
                                    <option value="" disabled selected>Pilih Bulan</option>
                                    @foreach ($months as $key => $month)
                                    <option value="{{ $key }}" {{ old('bulan', $bulan) == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                    
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tahun">Pilih Tahun</label>
                                <select name="tahun" id="tahun" class="form-control" required>
                                    <option value="" disabled selected>Pilih Tahun</option>
                                    @php
                                        $currentYear = date('Y');
                                    @endphp
                                    @for ($i = $currentYear - 1; $i <= $currentYear + 1; $i++)
                                        <option value="{{ $i }}" {{ old('tahun', $tahun) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
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
@endsection
