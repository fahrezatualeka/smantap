@extends('layout/template')
@section('content')

<style>
    .table-responsive {
        max-height: 570px;

        overflow-y: auto;
        }

</style>

<section class="content-header">
    <h1><b>Piutang</b></h1>
</section>

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



<section class="content">
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            Data Piutang Petugas Penagihan (Zona {{ Auth::user()->zona ?? 'Tidak Ditentukan' }})
        </h3>
    </div>

    <div class="box-body">
        <form action="{{ route('petugas_penagihan.data_penagihan.filter') }}" method="GET" id="filterForm">
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
                    <th>Telepon</th>
                    <th>Periode</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataPenagihan as $key => $piutang)
                    <tr data-toggle="modal" data-target="#paymentModal{{ $piutang->id }}" style="cursor: pointer;">
                        <td><b>{{ $key + 1 }}</b></td>
                        <td><b>{{ $piutang->nama_pajak }}</b></td>
                        <td><b>{{ $piutang->alamat }}</b></td>
                        <td><b>{{ $piutang->npwpd }}</b></td>
                        <td><b>{{ $piutang->jenisPajak->jenispajak ?? 'N/A' }}</b></td>
                        <td><b>{{ $piutang->telepon }}</b></td>
                        <td><b>{{ $piutang->periode }}</b></td>
                    </tr>
            
    
                <!-- Modal Pembayaran -->
                <div class="modal fade" id="paymentModal{{ $piutang->id }}" data-piutang-id="{{ $piutang->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel{{ $piutang->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-center" id="paymentModalLabel{{ $piutang->id }}">
                                    <b>Form Pembayaran Petugas</b>
                                    <p>Wajib Pajak {{ $piutang->nama_pajak }}</p>
                                </h4>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('data_penagihan.updateStatus', $piutang->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="Sudah Bayar">
                
                                    <div class="form-group">
                                        <label for="nama_pajak">Nama Pajak</label>
                                        <input type="text" name="nama_pajak" id="nama_pajak" class="form-control" value="{{ $piutang->nama_pajak }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="npwpd">NPWPD</label>
                                        <input type="text" name="npwpd" id="npwpd" class="form-control" value="{{ $piutang->npwpd }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="periode">Periode Piutang Pajak</label>
                                        <input type="text" name="periode" id="periode" class="form-control" value="{{ $piutang->periode }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="metode_pembayaran">Metode Pembayaran</label>
                                        <select name="metode_pembayaran" id="metode-pembayaran-{{ $piutang->id }}" class="form-control" required>
                                            <option value="">- Pilih -</option>
                                            <option value="Transfer">Transfer WP</option>
                                            <option value="Tunai">Tunai Petugas</option>
                                            <option value="Konfirmasi">Konfirmasi</option>
                                            <option value="Penutupan">WP Nonaktif</option>
                                        </select>
                                    </div>
                                    {{-- <div class="form-group" id="buktiSspdGroup">
                                        <label for="buktisspd">Bukti SSPD</label>
                                        <input type="hidden" name="foto_result_sspd" id="foto_result_sspd-{{ $piutang->id }}">
                                        <br>
                                        <button id="open-webcam-{{ $piutang->id }}-sspd" class="btn btn-secondary" type="button">
                                            <i class="fa fa-camera"></i> Foto
                                        </button>
                                        <br>
                                        <div id="webcam-container-{{ $piutang->id }}-sspd" style="display: none; position: relative;">
                                            <video id="video-webcam-{{ $piutang->id }}-sspd" autoplay style="width: 100%; max-width: 400px; border: 2px solid #ccc;"></video>
                                            <img id="foto-hasil-{{ $piutang->id }}-sspd" style="display: none; position: absolute; top: 0; left: 0; width: 100%; max-width: 400px; z-index: 10;" />
                                            <br>
                                            <button id="capture-photo-{{ $piutang->id }}-sspd" class="btn btn-warning" type="button" style="margin-top: 10px;">
                                                <i class="fa fa-camera"></i> Ambil Gambar
                                            </button>
                                            <button id="retake-photo-{{ $piutang->id }}-sspd" class="btn btn-info" type="button" style="display: none; margin-top: 10px;">
                                                <i class="fa fa-repeat"></i> Foto Ulang
                                            </button>
                                        </div>
                                    </div> --}}

                                    <div class="form-group" id="jumlahPembayaranGroup" style="display: none;">
                                        <label for="jumlah_pembayaran">Jumlah Pembayaran</label>
                                        <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran" class="form-control" value="{{ $piutang->jumlah_pembayaran }}">
                                    </div>

                                    <div class="form-group" id="buktiPembayaranGroup">
                                        <label for="buktipembayaran">Bukti Pembayaran</label>
                                        <input type="hidden" name="foto_result_pembayaran" id="foto_result_pembayaran-{{ $piutang->id }}">
                                        <br>
                                        <button id="open-webcam-{{ $piutang->id }}-pembayaran" class="btn btn-secondary" type="button">
                                            <i class="fa fa-camera"></i> Foto
                                        </button>
                                        <br>
                                        <div id="webcam-container-{{ $piutang->id }}-pembayaran" style="display: none; position: relative;">
                                            <video id="video-webcam-{{ $piutang->id }}-pembayaran" autoplay style="width: 100%; max-width: 400px; border: 2px solid #ccc;"></video>
                                            <img id="foto-hasil-{{ $piutang->id }}-pembayaran" style="display: none; position: absolute; top: 0; left: 0; width: 100%; max-width: 400px; z-index: 10;" />
                                            <br>
                                            <button id="capture-photo-{{ $piutang->id }}-pembayaran" class="btn btn-warning" type="button" style="margin-top: 10px;">
                                                <i class="fa fa-camera"></i> Ambil Gambar
                                            </button>
                                            <button id="retake-photo-{{ $piutang->id }}-pembayaran" class="btn btn-info" type="button" style="display: none; margin-top: 10px;">
                                                <i class="fa fa-repeat"></i> Foto Ulang
                                            </button>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="form-group" id="buktiVisitGroup">
                                        <label for="buktipembayaran">Bukti Visit</label>
                                        <input type="hidden" name="foto_result_visit" id="foto_result_visit-{{ $piutang->id }}">
                                        <br>
                                        <button id="open-webcam-{{ $piutang->id }}-visit" class="btn btn-secondary" type="button">
                                            <i class="fa fa-camera"></i> Foto
                                        </button>
                                        <br>
                                        <div id="webcam-container-{{ $piutang->id }}-visit" style="display: none; position: relative;">
                                            <video id="video-webcam-{{ $piutang->id }}-visit" autoplay style="width: 100%; max-width: 400px; border: 2px solid #ccc;"></video>
                                            <img id="foto-hasil-{{ $piutang->id }}-visit" style="display: none; position: absolute; top: 0; left: 0; width: 100%; max-width: 400px; z-index: 10;" />
                                            <br>
                                            <button id="capture-photo-{{ $piutang->id }}-visit" class="btn btn-warning" type="button" style="margin-top: 10px;">
                                                <i class="fa fa-camera"></i> Ambil Gambar
                                            </button>
                                            <button id="retake-photo-{{ $piutang->id }}-visit" class="btn btn-info" type="button" style="display: none; margin-top: 10px;">
                                                <i class="fa fa-repeat"></i> Foto Ulang
                                            </button>
                                            
                                        </div>
                                    </div>

                                    <div class="form-group" id="buktiPenutupanGroup" style="display: none;">
                                        <label for="buktipenutupan">Bukti WP Nonaktif</label>
                                        <input type="hidden" name="foto_result_penutupan" id="foto_result_penutupan-{{ $piutang->id }}">
                                        <br>
                                        <button id="open-webcam-{{ $piutang->id }}-penutupan" class="btn btn-secondary" type="button">
                                            <i class="fa fa-camera"></i> Foto
                                        </button>
                                        <br>
                                        <div id="webcam-container-{{ $piutang->id }}-penutupan" style="display: none; position: relative;">
                                            <video id="video-webcam-{{ $piutang->id }}-penutupan" autoplay style="width: 100%; max-width: 400px; border: 2px solid #ccc;"></video>
                                            <img id="foto-hasil-{{ $piutang->id }}-penutupan" style="display: none; position: absolute; top: 0; left: 0; width: 100%; max-width: 400px; z-index: 10;" />
                                            <br>
                                            <button id="capture-photo-{{ $piutang->id }}-penutupan" class="btn btn-warning" type="button" style="margin-top: 10px;">
                                                <i class="fa fa-camera"></i> Ambil Gambar
                                            </button>
                                            <button id="retake-photo-{{ $piutang->id }}-penutupan" class="btn btn-info" type="button" style="display: none; margin-top: 10px;">
                                                <i class="fa fa-repeat"></i> Foto Ulang
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group" id="keteranganGroup" style="display: none;">
                                        <label for="keterangan">Keterangan</label>
                                        <input type="text" name="keterangan" id="keterangan" class="form-control">
                                    </div>

                                    <input type="hidden" name="foto_result" id="foto_result-{{ $piutang->id }}">
                                    <button type="submit" id="submitButton{{ $piutang->id }}" class="btn btn-success" onclick="return confirm('Apakah anda yakin dengan data ini?')">
                                        <i class="fa fa-paper-plane"></i> Kirim
                                    </button>
                                </form>
                            </div>
                            {{-- @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif --}}
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data piutang.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>


      


    <br>
    <div class="box-footer text-left">
        {{-- <a href="{{ route('data_penagihan.exportExcel') }}" class="btn bg-black">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a> --}}
        {{-- <a href="{{ route('petugas_penagihan.data_penagihan.exportPdf') }}" class="btn bg-black">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a> --}}
        <a href="{{ route('petugas_penagihan.data_penagihan.exportPdf', [
            'search' => request('search'), 
            'jenis_pajak_id' => request('jenis_pajak_id'),
            'bulan' => request('bulan')]) }}" class="btn bg-black">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>

</div>
</section>
<script>


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".modal").forEach((modal) => {
        const modalId = modal.getAttribute("id").replace("paymentModal", "");

        // Selector for modal elements
        const metodeSelect = modal.querySelector(`#metode-pembayaran-${modalId}`);
        const buktiPembayaranGroup = modal.querySelector(`#buktiPembayaranGroup`);
        const buktiVisitGroup = modal.querySelector(`#buktiVisitGroup`);
        const jumlahPembayaranGroup = modal.querySelector(`#jumlahPembayaranGroup`);
        const keteranganGroup = modal.querySelector(`#keteranganGroup`);

        const buktiPenutupanGroup = modal.querySelector(`#buktiPenutupanGroup`);
        const webcamContainerPenutupan = modal.querySelector(`#webcam-container-${modalId}-penutupan`);
        const videoPenutupan = modal.querySelector(`#video-webcam-${modalId}-penutupan`);
        const buktiPenutupanButton = modal.querySelector(`#open-webcam-${modalId}-penutupan`);
        const ambilGambarButtonPenutupan = modal.querySelector(`#capture-photo-${modalId}-penutupan`);
        const retakePhotoButtonPenutupan = modal.querySelector(`#retake-photo-${modalId}-penutupan`);
        const fotoHasilPenutupan = modal.querySelector(`#foto-hasil-${modalId}-penutupan`);

        // Webcam containers and buttons
        const webcamContainerPembayaran = modal.querySelector(`#webcam-container-${modalId}-pembayaran`);
        const webcamContainerVisit = modal.querySelector(`#webcam-container-${modalId}-visit`);

        const videoPembayaran = modal.querySelector(`#video-webcam-${modalId}-pembayaran`);
        const videoVisit = modal.querySelector(`#video-webcam-${modalId}-visit`);

        const buktiPembayaranButton = modal.querySelector(`#open-webcam-${modalId}-pembayaran`);
        const buktiVisitButton = modal.querySelector(`#open-webcam-${modalId}-visit`);

        const ambilGambarButtonPembayaran = modal.querySelector(`#capture-photo-${modalId}-pembayaran`);
        const ambilGambarButtonVisit = modal.querySelector(`#capture-photo-${modalId}-visit`);

        const retakePhotoButtonPembayaran = modal.querySelector(`#retake-photo-${modalId}-pembayaran`);
        const retakePhotoButtonVisit = modal.querySelector(`#retake-photo-${modalId}-visit`);

        const fotoHasilPembayaran = modal.querySelector(`#foto-hasil-${modalId}-pembayaran`);
        const fotoHasilVisit = modal.querySelector(`#foto-hasil-${modalId}-visit`);

        let stream;

        // Reset display on modal open
        resetGroups();

        // Event listener for method change
        metodeSelect.addEventListener("change", function () {
            resetGroups();
            const selectedValue = metodeSelect.value;

            // Display relevant sections based on selected payment method
            if (selectedValue === "Transfer" || selectedValue === "Tunai") {
                buktiPembayaranGroup.style.display = "block";
                jumlahPembayaranGroup.style.display = "block";
                keteranganGroup.style.display = "block";
            } else if (selectedValue === "Konfirmasi") {
                buktiVisitGroup.style.display = "block";
                keteranganGroup.style.display = "block";
            } else if (selectedValue === "Penutupan") {
                buktiPenutupanGroup.style.display = "block";
                keteranganGroup.style.display = "block";
            }

            // Reset webcam and photo buttons for new method
            resetWebcamTombol();
        });

        // Start webcam function with rear camera preference
        async function startWebcam(videoElement, container, button, alternateButton) {
            try {
                stopWebcam(button);

                // Get all video devices
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === "videoinput");

                // Select the rear camera if available
                let rearCamera = videoDevices.find(device => device.label.toLowerCase().includes("back"));

                // If no rear camera is found, use the first available camera
                const deviceId = rearCamera ? rearCamera.deviceId : videoDevices[0].deviceId;

                // Start the webcam with the selected camera
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { deviceId: deviceId }
                });

                videoElement.srcObject = stream;
                container.style.display = "block";
                button.style.display = "none";

                if (alternateButton) alternateButton.style.display = "inline-block";
            } catch (error) {
                console.error("Webcam error:", error);
                alert("Unable to access the webcam. Please allow the permission.");
            }
        }

        // Stop webcam function
        function stopWebcam(button) {
            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
                stream = null;
            }

            // Hide webcam containers
            [webcamContainerPembayaran, webcamContainerVisit, webcamContainerPenutupan].forEach((container) => {
                if (container) container.style.display = "none";
            });

            if (button) button.style.display = "inline-block";
        }

        // Reset display and hide groups
        function resetGroups() {
            [buktiPembayaranGroup, buktiVisitGroup, buktiPenutupanGroup, keteranganGroup, jumlahPembayaranGroup].forEach((element) => {
                if (element) element.style.display = "none";
            });
            [webcamContainerPembayaran, webcamContainerVisit, webcamContainerPenutupan].forEach((container) => {
                if (container) container.style.display = "none";
            });

            // Reset buttons
            [buktiPembayaranButton, buktiVisitButton].forEach((button) => {
                if (button) button.style.display = "inline-block";
            });

            // Reset photos and stop webcam
            [fotoHasilPembayaran, fotoHasilVisit].forEach((foto) => {
                if (foto) {
                    foto.style.display = "none";
                    foto.src = "";
                }
            });

            stopWebcam();
        }

        // Capture photo and display result
        function capturePhoto(videoElement, photoElement, hiddenInputId, captureButton, retakeButton) {
            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL("image/png");
            photoElement.src = dataUrl;
            photoElement.style.display = "block";
            document.getElementById(hiddenInputId).value = dataUrl;

            // Hide capture button and show retake button
            captureButton.style.display = "none";
            retakeButton.style.display = "inline-block";
        }

        // Menampilkan kamera saat tombol foto ditekan
        buktiPembayaranButton?.addEventListener("click", function () {
            startWebcam(videoPembayaran, webcamContainerPembayaran, buktiPembayaranButton, retakePhotoButtonPembayaran);
        });

        buktiVisitButton?.addEventListener("click", function () {
            startWebcam(videoVisit, webcamContainerVisit, buktiVisitButton, retakePhotoButtonVisit);
        });

        buktiPenutupanButton?.addEventListener("click", function () {
            startWebcam(videoPenutupan, webcamContainerPenutupan, buktiPenutupanButton, retakePhotoButtonPenutupan);
        });

        // Ambil foto dan simpan di input hidden untuk pembayaran
        ambilGambarButtonPembayaran?.addEventListener('click', function () {
            capturePhoto(videoPembayaran, fotoHasilPembayaran, `foto_result_pembayaran-${modalId}`, ambilGambarButtonPembayaran, retakePhotoButtonPembayaran);
        });

        // Ambil foto untuk visit
        ambilGambarButtonVisit?.addEventListener('click', function () {
            capturePhoto(videoVisit, fotoHasilVisit, `foto_result_visit-${modalId}`, ambilGambarButtonVisit, retakePhotoButtonVisit);
        });

        // Ambil foto untuk penutupan
        ambilGambarButtonPenutupan?.addEventListener('click', function () {
            capturePhoto(videoPenutupan, fotoHasilPenutupan, `foto_result_penutupan-${modalId}`, ambilGambarButtonPenutupan, retakePhotoButtonPenutupan);
        });
    });
});

// document.addEventListener("DOMContentLoaded", function () {
//     document.querySelectorAll(".modal").forEach((modal) => {
//         const modalId = modal.getAttribute("id").replace("paymentModal", "");

//         // Selector for modal elements
//         const metodeSelect = modal.querySelector(`#metode-pembayaran-${modalId}`);
//         const buktiPembayaranGroup = modal.querySelector(`#buktiPembayaranGroup`);
//         const buktiVisitGroup = modal.querySelector(`#buktiVisitGroup`);
//         const jumlahPembayaranGroup = modal.querySelector(`#jumlahPembayaranGroup`);
//         const keteranganGroup = modal.querySelector(`#keteranganGroup`);

//         const buktiPenutupanGroup = modal.querySelector(`#buktiPenutupanGroup`);
//         const webcamContainerPenutupan = modal.querySelector(`#webcam-container-${modalId}-penutupan`);
//         const videoPenutupan = modal.querySelector(`#video-webcam-${modalId}-penutupan`);
//         const buktiPenutupanButton = modal.querySelector(`#open-webcam-${modalId}-penutupan`);
//         const ambilGambarButtonPenutupan = modal.querySelector(`#capture-photo-${modalId}-penutupan`);
//         const retakePhotoButtonPenutupan = modal.querySelector(`#retake-photo-${modalId}-penutupan`);
//         const fotoHasilPenutupan = modal.querySelector(`#foto-hasil-${modalId}-penutupan`);

//         // Webcam containers and buttons
//         const webcamContainerPembayaran = modal.querySelector(`#webcam-container-${modalId}-pembayaran`);
//         const webcamContainerVisit = modal.querySelector(`#webcam-container-${modalId}-visit`);

//         const videoPembayaran = modal.querySelector(`#video-webcam-${modalId}-pembayaran`);
//         const videoVisit = modal.querySelector(`#video-webcam-${modalId}-visit`);

//         const buktiPembayaranButton = modal.querySelector(`#open-webcam-${modalId}-pembayaran`);
//         const buktiVisitButton = modal.querySelector(`#open-webcam-${modalId}-visit`);

//         const ambilGambarButtonPembayaran = modal.querySelector(`#capture-photo-${modalId}-pembayaran`);
//         const ambilGambarButtonVisit = modal.querySelector(`#capture-photo-${modalId}-visit`);

//         const retakePhotoButtonPembayaran = modal.querySelector(`#retake-photo-${modalId}-pembayaran`);
//         const retakePhotoButtonVisit = modal.querySelector(`#retake-photo-${modalId}-visit`);

//         const fotoHasilPembayaran = modal.querySelector(`#foto-hasil-${modalId}-pembayaran`);
//         const fotoHasilVisit = modal.querySelector(`#foto-hasil-${modalId}-visit`);

//         let stream;

//         // Reset display on modal open
//         resetGroups();

//         // Event listener for method change
//         metodeSelect.addEventListener("change", function () {
//             resetGroups();
//             const selectedValue = metodeSelect.value;

//             // Display relevant sections based on selected payment method
//             if (selectedValue === "Transfer" || selectedValue === "Tunai") {
//                 buktiPembayaranGroup.style.display = "block";
//                 jumlahPembayaranGroup.style.display = "block";
//                 keteranganGroup.style.display = "block";
//             } else if (selectedValue === "Konfirmasi") {
//                 buktiVisitGroup.style.display = "block";
//                 keteranganGroup.style.display = "block";
//             } else if (selectedValue === "Penutupan") {
//                 buktiPenutupanGroup.style.display = "block";
//                 keteranganGroup.style.display = "block";
//             }

//             // Reset webcam and photo buttons for new method
//             resetWebcamTombol();
//         });

//         // Start webcam function with rear camera preference
//         async function startWebcam(videoElement, container, button, alternateButton) {
//             try {
//                 stopWebcam(button);

//                 // Get all video devices
//                 const devices = await navigator.mediaDevices.enumerateDevices();
//                 const videoDevices = devices.filter(device => device.kind === "videoinput");

//                 // Select the rear camera if available
//                 let rearCamera = videoDevices.find(device => device.label.toLowerCase().includes("back"));

//                 // If no rear camera is found, use the first available camera
//                 const deviceId = rearCamera ? rearCamera.deviceId : videoDevices[0].deviceId;

//                 // Start the webcam with the selected camera
//                 stream = await navigator.mediaDevices.getUserMedia({
//                     video: { deviceId: deviceId }
//                 });

//                 videoElement.srcObject = stream;
//                 container.style.display = "block";
//                 button.style.display = "none"; // Hide the "Ambil Gambar" button
//                 alternateButton.style.display = "inline-block"; // Show the "Foto Ulang" button
//             } catch (error) {
//                 console.error("Webcam error:", error);
//                 alert("Unable to access the webcam. Please allow the permission.");
//             }
//         }

//         // Stop webcam function
//         function stopWebcam(button) {
//             if (stream) {
//                 stream.getTracks().forEach((track) => track.stop());
//                 stream = null;
//             }

//             // Hide webcam containers
//             [webcamContainerPembayaran, webcamContainerVisit, webcamContainerPenutupan].forEach((container) => {
//                 if (container) container.style.display = "none";
//             });

//             if (button) button.style.display = "inline-block";
//         }

//         // Reset display and hide groups
//         function resetGroups() {
//             [buktiPembayaranGroup, buktiVisitGroup, buktiPenutupanGroup, keteranganGroup, jumlahPembayaranGroup].forEach((element) => {
//                 if (element) element.style.display = "none";
//             });
//             [webcamContainerPembayaran, webcamContainerVisit, webcamContainerPenutupan].forEach((container) => {
//                 if (container) container.style.display = "none";
//             });

//             // Reset buttons
//             [buktiPembayaranButton, buktiVisitButton].forEach((button) => {
//                 if (button) button.style.display = "inline-block";
//             });

//             // Reset photos and stop webcam
//             [fotoHasilPembayaran, fotoHasilVisit].forEach((foto) => {
//                 if (foto) {
//                     foto.style.display = "none";
//                     foto.src = "";
//                 }
//             });

//             stopWebcam();
//         }

//         // Capture photo and display result
//         function capturePhoto(videoElement, photoElement, hiddenInputId, captureButton, retakeButton) {
//             const canvas = document.createElement("canvas");
//             const context = canvas.getContext("2d");
//             canvas.width = videoElement.videoWidth;
//             canvas.height = videoElement.videoHeight;
//             context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

//             const dataUrl = canvas.toDataURL("image/png");
//             photoElement.src = dataUrl;
//             photoElement.style.display = "block";
//             document.getElementById(hiddenInputId).value = dataUrl;

//             // Hide capture button and show retake button
//             captureButton.style.display = "none";
//             retakeButton.style.display = "inline-block";
//         }

//         // Menampilkan kamera saat tombol foto ditekan
//         buktiPembayaranButton?.addEventListener("click", function () {
//             startWebcam(videoPembayaran, webcamContainerPembayaran, buktiPembayaranButton, retakePhotoButtonPembayaran);
//         });

//         buktiVisitButton?.addEventListener("click", function () {
//             startWebcam(videoVisit, webcamContainerVisit, buktiVisitButton, retakePhotoButtonVisit);
//         });

//         buktiPenutupanButton?.addEventListener("click", function () {
//             startWebcam(videoPenutupan, webcamContainerPenutupan, buktiPenutupanButton, retakePhotoButtonPenutupan);
//         });

//         // Ambil foto dan simpan di input hidden untuk pembayaran
//         ambilGambarButtonPembayaran?.addEventListener('click', function () {
//             capturePhoto(videoPembayaran, fotoHasilPembayaran, `foto_result_pembayaran-${modalId}`, ambilGambarButtonPembayaran, retakePhotoButtonPembayaran);
//         });

//         // Ambil foto untuk visit
//         ambilGambarButtonVisit?.addEventListener('click', function () {
//             capturePhoto(videoVisit, fotoHasilVisit, `foto_result_visit-${modalId}`, ambilGambarButtonVisit, retakePhotoButtonVisit);
//         });

//         // Ambil foto untuk penutupan
//         ambilGambarButtonPenutupan?.addEventListener('click', function () {
//             capturePhoto(videoPenutupan, fotoHasilPenutupan, `foto_result_penutupan-${modalId}`, ambilGambarButtonPenutupan, retakePhotoButtonPenutupan);
//         });

//         // Fungsi untuk tombol foto ulang, kembali ke tampilan webcam
//         function resetPhoto(photoElement, videoElement, captureButton, retakeButton, container) {
//             photoElement.style.display = "none";
//             captureButton.style.display = "inline-block";
//             retakeButton.style.display = "none";
//             container.style.display = "block";
//             videoElement.srcObject = stream; // Restores webcam feed
//         }

//         // Tombol Foto Ulang untuk Pembayaran
//         retakePhotoButtonPembayaran?.addEventListener("click", function () {
//             resetPhoto(fotoHasilPembayaran, videoPembayaran, ambilGambarButtonPembayaran, retakePhotoButtonPembayaran, webcamContainerPembayaran);
//         });

//         // Tombol Foto Ulang untuk Visit
//         retakePhotoButtonVisit?.addEventListener("click", function () {
//             resetPhoto(fotoHasilVisit, videoVisit, ambilGambarButtonVisit, retakePhotoButtonVisit, webcamContainerVisit);
//         });

//         // Tombol Foto Ulang untuk Penutupan
//         retakePhotoButtonPenutupan?.addEventListener("click", function () {
//             resetPhoto(fotoHasilPenutupan, videoPenutupan, ambilGambarButtonPenutupan, retakePhotoButtonPenutupan, webcamContainerPenutupan);
//         });
//     });
// });

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


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal').forEach(modal => {
        const form = modal.querySelector('form');
        const metodePembayaran = modal.querySelector('[id^="metode-pembayaran"]');
        const jumlahPembayaran = modal.querySelector('[id^="jumlah_pembayaran"]');
        const buktiPembayaran = modal.querySelector('[id^="foto_result_pembayaran"]');
        const buktiSspd = modal.querySelector('[id^="foto_result_sspd"]');
        const buktiVisit = modal.querySelector('[id^="foto_result_visit"]');
        const buktiPenutupan = modal.querySelector('[id^="foto_result_penutupan"]');
        const keteranganGroup = modal.querySelector('#keteranganGroup');

        form.addEventListener('submit', function(event) {
    const metode = metodePembayaran.value; // Ambil metode pembayaran yang dipilih
    let errorMessages = [];

    // Ambil elemen foto pembayaran dan visit
    const fotoPembayaranValue = buktiPembayaran.value; 
    const fotoVisitValue = buktiVisit.value;
    const fotoPenutupanValue = buktiPenutupan.value;

    // Validasi berdasarkan metode pembayaran
    if (metode === 'Transfer' || metode === 'Tunai') {
        if (!jumlahPembayaran.value) {
            errorMessages.push('Jumlah Pembayaran wajib diisi.');
        }
        if (!fotoPembayaranValue) {
            errorMessages.push('Bukti Pembayaran wajib difoto.');
        }
    } else if (metode === 'Konfirmasi') {
        if (!fotoVisitValue) {
            errorMessages.push('Bukti Visit wajib difoto.');
        }
    } else if (metode === 'Penutupan') {
        if (!fotoPenutupanValue) {
            errorMessages.push('Bukti WP Nonaktif wajib difoto.');
        }
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
</script>
@endsection