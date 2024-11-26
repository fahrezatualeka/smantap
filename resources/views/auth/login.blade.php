<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMANTAPP | Pemerintah Kota Ambon</title>
  {{-- <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"> --}}
  <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/iCheck/square/blue.css') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  {{-- Notif Mengembang --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .login-box {
      width: 100%;
      max-width: 575px; /* Ukuran maksimal kolom login */
      min-width: 320px; /* Ukuran minimal kolom login */
      margin: 1 auto; /* Memastikan posisi di tengah layar */
    }

    @media (max-width: 768px) {
      .login-box {
        width: 100%; /* Kolom login mengambil 90% dari lebar layar di perangkat dengan lebar kecil */
      }
    }

    .login-logo p {
      font-size: 22px; /* Ukuran font yang lebih responsif pada logo dan teks */
    }
  </style>
</head>

<body class="login-page">
  {{-- hold-transition bg-brown --}}
<div class="login-box" style="width:40%">
  <div class="login-logo">
    <img src="{{ asset('uploads/logo.png') }}" class="img-fluid" alt="logo" width="100px" height="100px">
    <p style="font-size:30px">
      <b> SMANTAPP </b> <br> SISTEM APLIKASI PENAGIHAN PIUTANG PAJAK PEMERINTAH KOTA AMBON
  </p>
  </div>
  <div class="col-md-1"></div>
  <div class="col-md-10">
  <div class="login-box-body">

    <form action="{{ route('proses.login') }}" method="POST">
      @csrf
      <br>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-user"></i></span>
          <input type="text" name="username" class="form-control" placeholder="Username" autofocus required>

      </div>
      <br>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-lock"></i></span>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <br>
    
      {{-- Menampilkan pesan error jika username salah --}}
      @if ($errors->has('username'))
          <div class="alert alert-danger">
              {{ $errors->first('username') }}
          </div>
      @endif
    
      {{-- Menampilkan pesan error jika password salah --}}
      @if ($errors->has('password'))
          <div class="alert alert-danger">
              {{ $errors->first('password') }}
          </div>
      @endif
    
      <div class="row">
          <div class="col-xs-12">
              <button type="submit" name="login" class="btn btn-primary btn-block btn-normal">Masuk</button>
          </div>
      </div>
      <br>
    </form>
    
    
  
  
  </div>
  </div>
</div>
<!-- Tambahkan sebelum tag </body> -->
@if(Session::has('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Maaf...',
        text: '{{ Session::get("error") }}',
    });
</script>
@endif


<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>

<!-- <script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script> -->
</body>
</html>
