@extends('layout/template')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <p style="text-align:center; font-size:20px">
                    <i> SELAMAT DATANG di</i>
                </p>
                <p style="text-align:center; font-size:25px">
                    <b> SISTEM APLIKASI PENAGIHAN PIUTANG PAJAK <br> PEMERINTAH KOTA AMBON </b>
                    <p style="text-align:center; font-size:18px">
                         {{ Auth::user()->role }}
                        ({{ Auth::user()->nama }})
                    </p>
                </p>
            </div>
            <div class="box-body">
                @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Login!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 3000
                    });
                </script>
                @endif
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <a href="{{url('wp')}}" style="color:black">
                <div class="info-box">
                    <span class="info-box-icon bg-white"><i class="fa fa-user"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b> Wajib Pajak </b></span>
                        <span class="info-box-number">{{ $jumlahDataWp }}</span>
                    </div>
                </div>
            </a>
            </div>
            
            <div class="col-md-6 col-sm-6 col-xs-12">
                <a href="{{url('wp_tipe')}}" style="color:black">
                <div class="info-box">
                    <span class="info-box-icon bg-brown"><i class="fa fa-user-circle-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><b> Jenis Pajak </b></span>
                        <span class="info-box-number">8</span>
                    </div>
                </div>
                </a>
            </div>
        </div> --}}

    </section>
@endsection