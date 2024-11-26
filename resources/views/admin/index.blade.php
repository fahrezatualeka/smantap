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

    </section>
@endsection