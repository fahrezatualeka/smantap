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
        <b>Tunai</b>
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
            Data Tunai Petugas Penagihan (Zona {{ Auth::user()->zona ?? 'Tidak Ditentukan' }})
        </h3>
    </div>

    
    <div class="box-body">
        <form action="{{ route('petugas_penagihan.data_tunai.filter') }}" method="GET" id="filterForm">
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
                    {{-- <th>Zona</th> --}}
                    {{-- <th>Tagihan</th> --}}
                    <th>Periode</th>
                    <th>Tanggal Pembayaran</th>
                    {{-- <th>Metode Pembayaran</th> --}}
                    <th>Jumlah Pembayaran</th>
                    <th>Bukti Pembayaran</th>
                    <th>Bukti SSPD</th>
                    {{-- <th>Pengiriman</th> --}}
                    <th>Keterangan</th>
                </tr>
        </thead>
            <tbody>
                @if($dataTunai->isEmpty())
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data tunai.</td>
                </tr>
                @else
                @foreach ($dataTunai as $key => $tunai)
                <tr>
                    <td><b>{{ $key + 1 }}</td>
                    <td><b>{{ $tunai->nama_pajak }}</td>
                    <td><b>{{ $tunai->alamat }}</td>
                    <td><b>{{ $tunai->npwpd }}</td>
                    <td><b>{{ $tunai->jenisPajak->jenispajak ?? 'N/A' }}</td>
                    {{-- <td><b>{{ $pelunasan->kategoriPajak->kategoripajak ?? 'N/A' }}</td>                     --}}
                    <td><b>{{ $tunai->telepon }}</td>
                    {{-- <td><b>{{ $pelunasan->zona }}</td> --}}
                    {{-- <td><b>Rp{{ number_format((float) $pelunasan->tagihan, 0, ',', '.') }}</td> --}}
                    <td><b>{{ $tunai->periode }}</td>

                    <td><b>{{ \Carbon\Carbon::parse($tunai->tanggal_pembayaran)->locale('id')->isoFormat('D MMMM YYYY') }}</td>

                    <td><b>Rp{{ number_format((float) $tunai->jumlah_pembayaran, 0, ',', '.') }}</td>

                    
        <td><b>
                    @if($tunai->buktipembayaran)
                    <a href="javascript:void(0);" 
                    class="btn btn-warning btn-xs" 
                    onclick="showImageProof('{{ route('petugas_penagihan.data_tunai.showPaymentProof', $tunai->id) }}', 'Bukti Pembayaran')">
                    <i class="fa-solid fa-eye"></i> Lihat
                    </a>
                @else
                <span>Tidak ada</span>
                @endif
            </td>

            <td><b>
                @if($tunai->buktisspd)
                    <a href="javascript:void(0);" 
                       class="btn btn-warning btn-xs" 
                       onclick="showImageProof('{{ route('petugas_penagihan.data_tunai.showSspdProof', $tunai->id) }}', 'Bukti SSPD')">
                       <i class="fa-solid fa-eye"></i> Lihat
                    </a>
                @else
                    <span class="text-danger">Tidak ada</span><br>
                    <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#uploadModal-{{ $tunai->id }}">
                        <i class="fa-solid fa-upload"></i> Foto SSPD
                    </button>
            
                    <!-- Modal -->
                    <div class="modal fade" id="uploadModal-{{ $tunai->id }}" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel-{{ $tunai->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center" id="paymentModalLabel{{ $tunai->id }}">
                                        <b>Form Pengiriman Bukti SSPD Data Tunai</b>
                                        <p>Wajib Pajak {{ $tunai->nama_pajak }}</p>
                                    </h4>
                                </div>
                                <form action="{{ route('petugas_penagihan.data_tunai.uploadSspdInline', $tunai->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="nama_pajak">Nama Pajak</label>
                                            <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $tunai->nama_pajak }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="npwpd">NPWPD</label>
                                            <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $tunai->npwpd }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Bukti SSPD</label>
                                            <br>
                                            <button id="take-photo-btn-{{ $tunai->id }}" type="button" class="btn btn-secondary" onclick="openWebcam({{ $tunai->id }})">
                                                <i class="fa fa-camera"></i> Foto
                                            </button>
                                            
                                            <div id="webcam-container-{{ $tunai->id }}" style="display:none; position: relative;">
                                                <video id="video-webcam-{{ $tunai->id }}-sspd" autoplay style="width: 100%; max-width: 400px; border: 2px solid #ccc;"></video><br>
                                                
                                                <!-- Tombol rotasi kamera (ikon) -->
                                                <button id="rotate-camera-btn-{{ $tunai->id }}" class="btn btn-secondary" type="button" style="position: absolute; top: 10px; right: 10px; z-index: 10;" onclick="rotateCamera({{ $tunai->id }})">
                                                    <i class="fa fa-sync"></i>
                                                </button>
                                            
                                                <button id="capture-photo-{{ $tunai->id }}-sspd" class="btn btn-warning" type="button" style="margin-top: 10px;" onclick="capturePhoto({{ $tunai->id }})">
                                                    <i class="fa fa-camera"></i> Ambil Gambar
                                                </button>
                                            </div>
                                            <div id="photo-preview-container-{{ $tunai->id }}" style="display:none;">
                                                <img id="photo-preview-{{ $tunai->id }}" src="" alt="Preview Foto" style="width: 100%; max-width: 400px; margin-top: 10px;"/>
                                                <button id="retake-photo-btn-{{ $tunai->id }}" class="btn btn-info" type="button" style="margin-top: 10px;" onclick="retakePhoto({{ $tunai->id }})">
                                                    <i class="fa fa-repeat"></i> Foto Ulang
                                                </button>
                                            </div>
                                            <input type="hidden" name="photo_data" id="photo-data-{{ $tunai->id }}">
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin dengan data ini?')">
                                            <i class="fa fa-paper-plane"></i> Kirim
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </td>

            {{-- <td><b>{{ $pelunasan->tempat_pembayaran }}</td> --}}
            <td><b>{{ $tunai->keterangan ?? 'Tidak ada' }}</td>



                </tr>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="box-footer text-left">
        {{-- <a href="{{ route('admin.laporan_pelunasan.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a> --}}
        
        <a href="{{ route('petugas_penagihan.data_tunai.exportPdf', [
            'search' => request('search'), 
            'jenis_pajak_id' => request('jenis_pajak_id'),
            'bulan' => request('bulan')]) }}" class="btn bg-black">
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


let currentCamera = 'environment'; // Default: kamera belakang

function openWebcam(id) {
    // Sembunyikan tombol foto
    const takePhotoBtn = document.getElementById(`take-photo-btn-${id}`);
    takePhotoBtn.style.display = "none";
    
    // Tampilkan kontainer webcam
    const webcamContainer = document.getElementById(`webcam-container-${id}`);
    webcamContainer.style.display = "block";

    // Menyiapkan video untuk webcam
    const video = document.getElementById(`video-webcam-${id}-sspd`);
    const previewContainer = document.getElementById(`photo-preview-container-${id}`);

    // Mulai stream webcam dengan kamera belakang (environment) sebagai default
    startWebcamStream(id, currentCamera);

    // Sembunyikan preview foto
    previewContainer.style.display = "none";
}

function startWebcamStream(id, camera) {
    const video = document.getElementById(`video-webcam-${id}-sspd`);

    // Tentukan constraint untuk kamera (depan atau belakang)
    const constraints = camera === 'user' ? { video: { facingMode: "user" } } : { video: { facingMode: "environment" } };

    // Mulai stream webcam
    navigator.mediaDevices.getUserMedia(constraints)
        .then((stream) => {
            video.srcObject = stream;
        })
        .catch((error) => {
            alert("Kamera tidak tersedia.");
        });
}

function rotateCamera(id) {
    // Ubah kamera (depan ke belakang atau sebaliknya)
    currentCamera = currentCamera === 'user' ? 'environment' : 'user';

    // Ganti stream video dengan kamera yang baru
    startWebcamStream(id, currentCamera);
}

function capturePhoto(id) {
    const video = document.getElementById(`video-webcam-${id}-sspd`);
    const canvas = document.createElement("canvas"); // Buat canvas dinamis
    const photoData = document.getElementById(`photo-data-${id}`);
    const photoPreview = document.getElementById(`photo-preview-${id}`);
    const captureButton = document.getElementById(`capture-photo-${id}-sspd`);
    const webcamContainer = document.getElementById(`webcam-container-${id}`);
    const previewContainer = document.getElementById(`photo-preview-container-${id}`);
    const retakeButton = document.getElementById(`retake-photo-btn-${id}`);

    // Ambil gambar dari webcam ke canvas
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert canvas image ke base64 dan simpan ke input tersembunyi
    photoData.value = canvas.toDataURL("image/png");

    // Tampilkan foto hasil gambar di preview
    photoPreview.src = canvas.toDataURL("image/png");
    previewContainer.style.display = "block"; // Tampilkan foto

    // Sembunyikan webcam dan tombol ambil gambar
    webcamContainer.style.display = "none"; // Sembunyikan webcam
    captureButton.style.display = "none"; // Sembunyikan tombol ambil gambar
    retakeButton.style.display = "block"; // Tampilkan tombol foto ulang
}

function retakePhoto(id) {
    // Tampilkan tombol foto lagi dan sembunyikan foto preview
    const video = document.getElementById(`video-webcam-${id}-sspd`);
    const webcamContainer = document.getElementById(`webcam-container-${id}`);
    const previewContainer = document.getElementById(`photo-preview-container-${id}`);
    const captureButton = document.getElementById(`capture-photo-${id}-sspd`);
    const retakeButton = document.getElementById(`retake-photo-btn-${id}`);

    // Sembunyikan foto preview dan tampilkan webcam
    previewContainer.style.display = "none";
    webcamContainer.style.display = "block";
    captureButton.style.display = "inline-block";
    retakeButton.style.display = "none";
}



document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal').forEach(modal => {
        const form = modal.querySelector('form');
        const buktiSspd = modal.querySelector('[id^="photo-data"]'); // ID untuk bukti SSPD

        form.addEventListener('submit', function(event) {
            let errorMessages = [];

            // Validasi: jika bukti SSPD tidak diunggah
            if (!buktiSspd || !buktiSspd.value) {
                errorMessages.push('Bukti SSPD wajib difoto.');
            }

            // Jika ada kesalahan, tampilkan pesan error dan hentikan submit
            if (errorMessages.length > 0) {
                event.preventDefault(); // Hentikan submit
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal dikirim!',
                    html: errorMessages.join('<br>'), // Menampilkan pesan error sebagai list
                    showConfirmButton: false, // Menghilangkan tombol konfirmasi
                    timer: 4000 // Menampilkan pesan selama 4 detik
                });
            }
        });
    });
});
</script>

@endsection