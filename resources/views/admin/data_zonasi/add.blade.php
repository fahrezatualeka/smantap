@extends('layout/template')
@section('content')

<section class="content-header">
    <h1>Zonasi</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i></a></li>
        <li class="active">Zonasi</li>
    </ol>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Tambah Data Zonasi</h3>
            <div class="pull-right">
                <a href="{{ url('data_zonasi') }}" class="btn btn-default btn-normal">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form id="wpForm" action="{{ route('data_zonasi.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="npwpd">NPWPD</label>
                            <select name="npwpd" id="npwpd" class="form-control">
                                <option value="">- Pilih -</option>
                                @foreach($datapiutang as $piutang)
                                    <option value="{{ $piutang->npwpd }}">{{ $piutang->npwpd }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama_usaha">Nama Usaha</label>
                            <input type="text" name="nama_usaha" id="nama" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="alamat_usaha">Alamat Usaha</label>
                            <input type="text" name="alamat_usaha" id="alamat_usaha" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="penanggung_jawab">Penanggung Jawab</label>
                            <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="alamat_penanggung_jawab">Alamat Penanggung Jawab</label>
                            <input type="text" name="alamat_penanggung_jawab" id="alamat_penanggung_jawab" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="kategori_pajak">Kategori Pajak</label>
                            <input type="text" name="kategori_pajak" id="kategori_pajak" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="piutang_ini">Piutang Ini</label>
                            <input type="text" name="piutang_ini" id="piutang_ini" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="piutang_lalu">Piutang Lalu</label>
                            <input type="text" name="piutang_lalu" id="piutang_lalu" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="kode_zonasi">Kode Zonasi</label>
                            <input type="number" name="kode_zonasi" id="kode_zonasi" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="nama_zonasi">Nama Zonasi</label>
                            <select name="nama_zonasi" id="nama_zonasi" class="form-control">
                                <option value="">- Pilih -</option>
                                <option value="kece">Kece</option>
                                <option value="ambon">Ambon</option>
                                <option value="stain">Stain</option>
                                <option value="poka">Poka</option>
                                <option value="batumerah">Batu Merah</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="petugas_penagihan">Petugas Penagihan</label>
                            <select name="petugas_penagihan" id="petugas_penagihan" class="form-control">
                                <option value="">- Pilih -</option>
                                @foreach($petugasPenagihan as $petugas)
                                    <option value="{{ $petugas->id }}">{{ $petugas->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        

                        {{-- Display Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Submit and Reset Buttons --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-normal">
                                <i class="fa fa-paper-plane"></i> Kirim
                            </button>
                            <button type="reset" class="btn btn-danger btn-normal">
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
    document.getElementById('npwpd').addEventListener('change', function() {
        let npwpd = this.value;

        if (npwpd) {
            fetch(`/data-piutang/${npwpd}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('nama').value = data.nama_usaha;
                        document.getElementById('alamat_usaha').value = data.alamat_usaha;
                        document.getElementById('penanggung_jawab').value = data.penanggung_jawab;
                        document.getElementById('alamat_penanggung_jawab').value = data.alamat_penanggung_jawab;
                        document.getElementById('kategori_pajak').value = data.kategori_pajak;
                        document.getElementById('piutang_ini').value = data.piutang_ini;
                        document.getElementById('piutang_lalu').value = data.piutang_lalu;
                    } else {
                        alert('Data tidak ditemukan');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>

@endsection
