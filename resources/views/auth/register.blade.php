<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Pajak Pemerintah Kota Ambon | Register</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  {{-- Logo Website --}}
  <link rel="icon" type="image/x-icon" href="{{ asset('uploads/logo.png') }}" />

  <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">r
  <link rel="stylesheet" href="{{ asset('plugins/iCheck/square/blue.css') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  {{-- Notif Mengembang --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="register-page">
<div class="register-box" style="width:40%">
  <div class="register-logo">
    <img src="{{ asset('uploads/logo.png') }}" class="img-fluid" alt="logo" width="100px" height="100px">
    <p style="font-size:30px">
      <b> WEBSITE MONITORING PENGELOLAAN PAJAK PEMERINTAH KOTA AMBON </b>
    </p>
  </div>
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <div class="register-box-body">

      @if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2500
    });
</script>
@endif

      <form action="/register/proses" method="POST">
        @csrf
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
            <input type="text" value="{{ Session::get('name') }}" name="name" class="form-control" placeholder="Nama Lengkap" autofocus required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
            <input type="text" name="email" class="form-control" placeholder="Email" autofocus required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i></span>
            <input type="text" value="{{ Session::get('username') }}" name="username" class="form-control" placeholder="Username" autofocus required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Password" autofocus required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
            <input type="number" name="no_telepon" class="form-control" placeholder="Nomor Telepon" autofocus required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-home"></i></span>
            <input type="text" name="alamat" class="form-control" placeholder="Alamat" autofocus required>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-building"></i></span>
            <input type="text" name="kode" class="form-control" placeholder="Kode Perusahaan" autofocus required>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12">
                <button type="submit" name="register" class="btn btn-success btn-normal"><i class="fa fa-paper-plane"></i>
                  Daftar
              </button>
                <button type="reset" name="register" class="btn btn-danger btn-normal"><i class="fa fa-refresh"></i>
                  Ulangi
              </button>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12">
              <p>Sudah punya Akun? Silahkan Masuk</p>
              <a href="/" class="btn btn-primary btn-block btn-normal">Masuk</a>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Maaf...',
                text: '{{ $error }}'
            });
        </script>
    @endforeach
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