@extends('layout/template')
@section('content')
    <section class="content-header">
        <h1>
            Piutang
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"> Edit Data Piutang </h3>
                <div class="pull-right">
                    <a href="{{ url('data_piutang') }}" class="btn btn-info btn-normal">
                        <i class="fa fa-arrow-left"></i> Kembali </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <form id="datapiutangForm" action="{{ route('admin.data_piutang.update', $datapiutang->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="form-group">
                                <label for="npwpd">NPWPD</label>
                                <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $datapiutang->npwpd }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="nama_pajak">Nama Pajak</label>
                                <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $datapiutang->nama_pajak }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" id="alamat" class="form-control" value="{{ $datapiutang->alamat }}" readonly>
                            </div>

                            {{-- <div class="form-group">
                                <label for="nomor_telepon">Nomor Telepon</label>
                                <input type="number" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ $datapiutang->nomor_telepon }}" readonly>
                            </div> --}}

                            <div class="form-group">
                                <label for="jenis_pajak_id">Jenis Pajak</label>
                                <input type="text" id="jenis_pajak_id" name="jenis_pajak_id" id="jenis_pajak_id" class="form-control" value="{{ $datapiutang->jenis_pajak_id }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="kategori_pajak_id">Kategori Pajak</label>
                                <input type="text" id="kategori_pajak_id" name="kategori_pajak_id" id="kategori_pajak_id" class="form-control" value="{{ $datapiutang->kategori_pajak_id }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="jumlah_piutang">Jumlah Piutang</label>
                                <input type="number" id="jumlah_piutang" name="jumlah_piutang" id="jumlah_piutang" class="form-control" value="{{ $datapiutang->jumlah_piutang }}">
                            </div>

                            <div class="form-group">
                                <label for="tanggal_tagihan">Tanggal Tagihan</label>
                                <input type="date" id="tanggal_tagihan" name="tanggal_tagihan" id="tanggal_tagihan" class="form-control" value="{{ $datapiutang->tanggal_tagihan }}">
                            </div>

                            <div class="form-group">
                                <label for="bulan">Bulan</label>
                                <input type="text" id="bulan" name="bulan" id="bulan" class="form-control" value="{{ $datapiutang->bulan }}" readonly>
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

                            @if(session('success'))
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: '{{ session('success') }}'
                                });
                            @endif
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection