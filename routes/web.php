<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Exports\WpExport;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\ContactController;
Route::resource('contacts', ContactController::class);
Route::get('contacts/{id}/send-message', [ContactController::class, 'sendMessage'])->name('contacts.sendMessage');

use App\Http\Controllers\MessageController;
Route::resource('messages', MessageController::class);
// PETUGAS PENAGIHAN
use App\Http\Controllers\IndexPetugasPenagihanController;
use App\Http\Controllers\DashboardPetugasPenagihanController;
use App\Http\Controllers\DataPenagihanController;
use App\Http\Controllers\DataTransferController;
use App\Http\Controllers\DataTunaiController;
use App\Http\Controllers\DataKonfirmasiController;
use App\Http\Controllers\DataPenutupanController;
// use App\Http\Controllers\DataPelunasanController;
use App\Http\Controllers\BuktiController;
use Illuminate\Support\Facades\File;




// ADMIN
use App\Http\Controllers\IndexAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataWajibPajakController;
use App\Http\Controllers\DataZonasiController;
// use App\Http\Controllers\DataPenetapanController;
use App\Http\Controllers\DataPiutangController;
use App\Http\Controllers\LaporanPenagihanController;
use App\Http\Controllers\LaporanPiutangController;
use App\Http\Controllers\LaporanPelunasanController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\JenisPajakController;
use App\Http\Controllers\KelolaPesanWhatsappController;
// use App\Http\Controllers\KategoriPajakController;
use App\Http\Controllers\ProfilAdminController;
use App\Http\Controllers\ProfilPetugasPenagihanController;
use App\Http\Controllers\ProfilPimpinanController;
use App\Http\Controllers\FonnteController;
// use App\Models\LaporanPelunasan;
use App\Http\Controllers\LaporanTransferController;
use App\Http\Controllers\LaporanTunaiController;
use App\Http\Controllers\LaporanKonfirmasiController;
use App\Http\Controllers\LaporanPenutupanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ROUTE Register
// Route::get('/register', [RegisterController::class, 'index']);
// Route::post('/register/proses', [RegisterController::class, 'store']);

// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
//     Route::get('/data_piutang', [DataPiutangController::class, 'index'])->name('data_piutang');
//     Route::get('/data_zonasi', [DataZonasiController::class, 'index'])->name('data_zonasi');
//     Route::get('/data_wajib_pajak', [DataWajibPajakController::class, 'index'])->name('data_wajib_pajak');
//     Route::get('/laporan_penagihan', [LaporanPenagihanController::class, 'index'])->name('laporan_penagihan');
//     Route::get('/data_user', [DataUserController::class, 'index'])->name('data_user');
// });


Route::get('file-access/{path}', function ($path) {
    $filePath = storage_path('app/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->name('access.file')->middleware('signed');

// LOGIN
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/proses', [LoginController::class, 'login'])->name('proses.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// PIMPINAN
Route::group(['middleware' => ['role:pimpinan']], function () {
    Route::get('/pimpinan', [IndexAdminController::class, 'index'])->name('pimpinan.index');
    Route::get('/dashboard_pimpinan', [DashboardController::class, 'index'])->name('dashboard');


    // profil pimpinan

    Route::get('/profil/pimpinan', [ProfilPimpinanController::class, 'index'])->name('pimpinan.profil');
    Route::put('/profil/pimpinan/update', [ProfilPimpinanController::class, 'update'])->name('pimpinan.profil.update');

});

// PETUGAS PENAGIHAN
Route::group(['middleware' => ['role:petugas_penagihan']], function () {
    Route::get('/petugas_penagihan', [IndexAdminController::class, 'index'])->name('petugas_penagihan.index');
    Route::get('/dashboard_petugaspenagihan', [DashboardPetugasPenagihanController::class, 'index'])->name('petugas_penagihan.dashboard');
    Route::get('/data_penagihan/filter', [DataPenagihanController::class, 'filter'])->name('petugas_penagihan.data_penagihan.filter');



    // WA PETUGAS
    Route::put('/data-penagihan/transfer/konfirmasi/{id}', [DataPenagihanController::class, 'updateKonfirmasi']);



    Route::get('/data_penagihan', [DataPenagihanController::class, 'index'])->name('petugas_penagihan.data_penagihan.data');
    Route::post('/data_penagihan/uploadBuktiPembayaran/{id}', [DataPenagihanController::class, 'uploadBuktiPembayaran'])->name('petugas_penagihan.data_penagihan.uploadBuktiPembayaran');
    // Route::post('/data_penagihan/markAsPaid/{id}', [DataPenagihanController::class, 'markAsPaid'])->name('petugas_penagihan.data_penagihan..markAsPaid');
    Route::delete('/datapenagihan/{id}', [DataPenagihanController::class, 'deleteDatapenagihan'])->name('petugas_penagihan.data_penagihan.delete');

    Route::get('/data_transfer', [DataTransferController::class, 'index'])->name('petugas_penagihan.data_transfer.data');
    Route::get('/data_tunai', [DataTunaiController::class, 'index'])->name('petugas_penagihan.data_tunai.data');

    Route::get('/data_konfirmasi', [DataKonfirmasiController::class, 'index'])->name('petugas_penagihan.data_konfirmasi.data');
    Route::get('/data_konfirmasi/{id}/bukti_visit', [DataKonfirmasiController::class, 'showVisitProof'])->name('petugas_penagihan.data_konfirmasi.showVisitProof');
    // Route::get('data-konfirmasi/visit-proof/{id}', [DataKonfirmasiController::class, 'showVisitProof'])
    // ->name('petugas_penagihan.data_konfirmasi.showVisitProof');
    Route::get('data_konfirmasi/exportPdf', [DataKonfirmasiController::class, 'exportPdf'])->name('petugas_penagihan.data_konfirmasi.exportPdf');
    Route::get('/data_konfirmasi/filter', [DataKonfirmasiController::class, 'filter'])->name('petugas_penagihan.data_konfirmasi.filter');


    Route::get('/data_penutupan', [DataPenutupanController::class, 'index'])->name('petugas_penagihan.data_penutupan.data');
    Route::get('/data_penutupan/{id}/bukti_visit', [DataPenutupanController::class, 'showPenutupanProof'])->name('petugas_penagihan.data_penutupan.showPenutupanProof');
    // Route::get('data-konfirmasi/visit-proof/{id}', [DataKonfirmasiController::class, 'showVisitProof'])
    // ->name('petugas_penagihan.data_konfirmasi.showVisitProof');
    Route::get('data_penutupan/exportPdf', [DataPenutupanController::class, 'exportPdf'])->name('petugas_penagihan.data_penutupan.exportPdf');
    Route::get('/data_penutupan/filter', [DataPenutupanController::class, 'filter'])->name('petugas_penagihan.data_penutupan.filter');
    // Route::get('/data_pelunasan', [DataPelunasanController::class, 'index'])->name('petugas_penagihan.data_pelunasan.data');
    // Route::put('/data_penagihan/{id}/update-status', [DataPenagihanController::class, 'updateStatus'])->name('data_penagihan.updateStatus');

    Route::get('/profil/petugas_penagihan', [ProfilPetugasPenagihanController::class, 'index'])->name('petugas_penagihan.profil');
    Route::put('/profil/petugas_penagihan/update', [ProfilPetugasPenagihanController::class, 'update'])->name('petugas_penagihan.profil.update');

    Route::get('/data_transfer/{id}/bukti_pembayaran', [DataTransferController::class, 'showPaymentProof'])->name('petugas_penagihan.data_transfer.showPaymentProof');
    Route::get('/data_transfer/{id}/bukti_sspd', [DataTransferController::class, 'showSspdProof'])->name('petugas_penagihan.data_transfer.showSspdProof');
    Route::post('/data-transfer/{id}/upload-sspd', [DataTransferController::class, 'uploadSspdInline'])->name('petugas_penagihan.data_transfer.uploadSspdInline');
    Route::get('data_transfer/exportPdf', [DataTransferController::class, 'exportPdf'])->name('petugas_penagihan.data_transfer.exportPdf');
    Route::get('/data_transfer/filter', [DataTransferController::class, 'filter'])->name('petugas_penagihan.data_transfer.filter');



    Route::post('/data-tunai/{id}/upload-sspd', [DataTunaiController::class, 'uploadSspdInline'])->name('petugas_penagihan.data_tunai.uploadSspdInline');
    Route::get('/data_tunai/{id}/bukti_pembayaran', [DataTunaiController::class, 'showPaymentProof'])->name('petugas_penagihan.data_tunai.showPaymentProof');
    Route::get('/data_tunai/{id}/bukti_sspd', [DataTunaiController::class, 'showSspdProof'])->name('petugas_penagihan.data_tunai.showSspdProof');
    Route::get('data_tunai/exportPdf', [DataTunaiController::class, 'exportPdf'])->name('petugas_penagihan.data_tunai.exportPdf');
    Route::get('/data_tunai/filter', [DataTunaiController::class, 'filter'])->name('petugas_penagihan.data_tunai.filter');




    


Route::post('/data_penagihan/upload/{id}', 
[DataPenagihanController::class, 'uploadBuktiPembayaran'])->name('data_penagihan.upload');
// Route::patch('data_penagihan/{id}/update-status', [DataPenagihanController::class, 'markAsPaid'])->name('data_penagihan.updateStatus');
Route::put('/data_penagihan/{id}/update-status', [DataPenagihanController::class, 'markAsPaid'])->name('data_penagihan.updateStatus');
// Route::get('data_penagihan/exportExcel', [DataPenagihanController::class, 'exportExcel'])->name('data_penagihan.exportExcel');
Route::get('data_penagihan/exportPdf', [DataPenagihanController::class, 'exportPdf'])->name('petugas_penagihan.data_penagihan.exportPdf');




    Route::get('/mark-as-read/{id}', function ($id) {
        auth()->user()->notifications->where('id', $id)->markAsRead();
        return redirect()->back();
    })->name('markAsRead');
});



// ADMIN

Route::get('/storage/uploads/{path}', function ($path) {
    $filePath = storage_path('app/public/uploads/' . $path);

    if (!File::exists($filePath)) {
        abort(404, 'File not found.');
    }

    $file = File::get($filePath);
    $type = File::mimeType($filePath);

    return response($file, 200)->header("Content-Type", $type);
})->where('path', '.*')->middleware('role:admin');
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin', [IndexAdminController::class, 'index'])->name('admin.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    // WAJIB PAJAK
    // Route::get('/data_wajibpajak/{npwpd}', [DataWajibPajakController::class, 'getDataByNpwpd']);
    Route::get('/data_wajibpajak/filter', [DataWajibPajakController::class, 'filter'])->name('admin.data_wajibpajak.filter');
    Route::get('/data_wajibpajak', [DataWajibPajakController::class, 'index'])->name('admin.data_wajibpajak.data');
    Route::post('/data_wajibpajak/import', [DataWajibPajakController::class, 'import'])->name('admin.data_wajibpajak.import');
    Route::get('/data_wajibpajak/add', [DataWajibPajakController::class, 'create'])->name('admin.data_wajibpajak.create');
    Route::post('/data_wajibpajak/add', [DataWajibPajakController::class, 'store'])->name('admin.data_wajibpajak.store');
    Route::get('/data_wajibpajak/edit/{id}', [DataWajibPajakController::class, 'edit'])->name('admin.data_wajibpajak.edit');
    Route::put('/data_wajibpajak/update/{id}', [DataWajibPajakController::class, 'update'])->name('admin.data_wajibpajak.update');
    Route::delete('/data_wajibpajak/delete/{id}', [DataWajibPajakController::class, 'destroy'])->name('admin.data_wajibpajak.delete');
    // Route::get('/admin/data_wajibpajak/kategori/{jenisPajakId}', [DataWajibPajakController::class, 'getKategoriPajak']);
    // Route::post('/admin/data_wajibpajak/updatePiutang', [DataWajibPajakController::class, 'updatePiutang'])->name('admin.data_wajibpajak.updatePiutang');
    // Route::post('/data_wajibpajak/updateTagihanPiutang', [DataWajibPajakController::class, 'updateTagihanPiutang'])->name('admin.data_wajibpajak.updateTagihanPiutang');
    // Route::post('/data_wajibpajak/zonasi', [DataWajibPajakController::class, 'zonasi'])->name('admin.data_wajibpajak.zonasi');
    Route::post('/data-wajib-pajak/mark-as-lunas', [DataWajibPajakController::class, 'markAsLunas'])->name('admin.data_wajibpajak.mark_as_lunas');
    Route::get('data_wajibpajak/exportExcel', [DataWajibPajakController::class, 'exportExcel'])->name('admin.data_wajibpajak.exportExcel');
    Route::get('data_wajibpajak/exportPdf', [DataWajibPajakController::class, 'exportPdf'])->name('admin.data_wajibpajak.exportPdf');
    


    // PENETAPAN
    Route::get('/data_piutang/filter', [DataPiutangController::class, 'filter'])->name('admin.data_piutang.filter');
    Route::get('/data_piutang', [DataPiutangController::class, 'index'])->name('admin.data_piutang.data');
    Route::post('/data_piutangn/import', [DataPiutangController::class, 'import'])->name('admin.data_piutang.import');
    // Route::get('/data_penetapan/index', [DataPenetapanController::class, 'index'])->name('admin.data_penetapan.index');
    Route::get('/data_piutang/add', [DataPiutangController::class, 'create'])->name('admin.data_piutang.create');
    Route::post('/data_piutang/add', [DataPiutangController::class, 'store'])->name('admin.data_piutang.store');
    Route::get('/data_piutang/edit/{id}', [DataPiutangController::class, 'edit'])->name('admin.data_piutang.edit');
    Route::put('/data_piutang/update/{id}', [DataPiutangController::class, 'update'])->name('admin.data_piutang.update');
    Route::delete('/data_piutang/delete/{id}', [DataPiutangController::class, 'destroy'])->name('admin.data_piutang.delete');
    // Route::put('/data_penetapan/{id}/status', [DataPenetapanController::class, 'updateStatus'])->name('admin.data_penetapan.updateStatus');
    Route::get('/pembayaran', [DataPiutangController::class, 'pembayaran'])->name('admin.data_piutang.pembayaran');
    // Route::get('/pembayaran', [DataPenetapanController::class, 'synchronizeToPiutang'])->name('admin.data_penetapan.synchronizeToPiutang');
    Route::put('/data_piutang/{id}/update-status', [DataPiutangController::class, 'updateStatus'])->name('admin.data_piutang.updateStatus');
    Route::get('data_piutang/exportExcel', [DataPiutangController::class, 'exportExcel'])->name('admin.data_piutang.exportExcel');
    Route::get('data_piutang/exportPdf', [DataPiutangController::class, 'exportPdf'])->name('admin.data_piutang.exportPdf');



    // PIUTANG
    // Route::get('/data_wajibpajak/{npwpd}', [DataWajibPajakController::class, 'getDataByNpwpd']);
    // Route::get('/data_piutang/filter', [DataPiutangController::class, 'filter'])->name('admin.data_piutang.filter');
    // Route::get('/data_piutang', [DataPiutangController::class, 'index'])->name('admin.data_piutang.data');
    // // Route::post('/data_piutang/import', [DataPiutangController::class, 'import'])->name('admin.data_piutang.import');
    // Route::post('data_piutang/zonasi', [DataPiutangController::class, 'saveZonasi'])->name('admin.data_piutang.saveZonasi');
    // Route::post('/data_piutang/import', [DataPiutangController::class, 'importFromDataPenetapan'])->name('admin.data_piutang.import');
    // Route::get('/data_piutang/add', [DataPiutangController::class, 'create'])->name('admin.data_piutang.create');
    // Route::post('/data_piutang/add', [DataPiutangController::class, 'store'])->name('admin.data_piutang.store');
    // Route::get('/data_piutang/edit/{id}', [DataPiutangController::class, 'edit'])->name('admin.data_piutang.edit');
    // Route::put('/data_piutang/update/{id}', [DataPiutangController::class, 'update'])->name('admin.data_piutang.update');
    // Route::delete('/data_piutang/delete/{id}', [DataPiutangController::class, 'destroy'])->name('admin.data_piutang.delete');
    // Route::get('data_piutang/exportExcel', [DataPiutangController::class, 'exportExcel'])->name('admin.data_piutang.exportExcel');
    // Route::get('data_piutang/exportPdf', [DataPiutangController::class, 'exportPdf'])->name('admin.data_piutang.exportPdf');



    // LAPORAN
    // PIUTANG
    Route::get('/laporan_piutang', [LaporanPiutangController::class, 'index'])->name('admin.laporan_piutang.data');
    Route::get('/laporan_piutang/filter', [LaporanPiutangController::class, 'filter'])->name('admin.laporan_piutang.filter');
    Route::get('laporan_piutang/exportExcel', [LaporanPiutangController::class, 'exportExcel'])->name('admin.laporan_piutang.exportExcel');
    Route::get('laporan_piutang/exportPdf', [LaporanPiutangController::class, 'exportPdf'])->name('admin.laporan_piutang.exportPdf');

    Route::get('/laporan_transfer', [LaporanTransferController::class, 'index'])->name('admin.laporan_transfer.data');
    Route::get('laporan_transfer/exportExcel', [LaporanTransferController::class, 'exportExcel'])->name('admin.laporan_transfer.exportExcel');
    Route::get('laporan_transfer/exportPdf', [LaporanTransferController::class, 'exportPdf'])->name('admin.laporan_transfer.exportPdf');

    Route::get('/laporan_tunai', [LaporanTunaiController::class, 'index'])->name('admin.laporan_tunai.data');
    Route::get('/laporan_tunai', [LaporanTunaiController::class, 'index'])->name('admin.laporan_tunai.data');
    Route::get('laporan_tunai/exportExcel', [LaporanTunaiController::class, 'exportExcel'])->name('admin.laporan_tunai.exportExcel');
    Route::get('laporan_tunai/exportPdf', [LaporanTunaiController::class, 'exportPdf'])->name('admin.laporan_tunai.exportPdf');

    Route::get('/laporan_konfirmasi', [LaporanKonfirmasiController::class, 'index'])->name('admin.laporan_konfirmasi.data');
    Route::get('laporan_konfirmasi/exportExcel', [LaporanKonfirmasiController::class, 'exportExcel'])->name('admin.laporan_konfirmasi.exportExcel');
    Route::get('laporan_konfirmasi/exportPdf', [LaporanKonfirmasiController::class, 'exportPdf'])->name('admin.laporan_konfirmasi.exportPdf');
    Route::get('/laporan_konfirmasi/{id}/bukti_visit', [LaporanKonfirmasiController::class, 'showVisitProof'])->name('admin.laporan_konfirmasi.showVisitProof');
    Route::get('/laporan_konfirmasi/filter', [LaporanKonfirmasiController::class, 'filter'])->name('admin.laporan_konfirmasi.filter');

    Route::get('/laporan_penutupan', [LaporanPenutupanController::class, 'index'])->name('admin.laporan_penutupan.data');
    Route::get('laporan_penutupan/exportExcel', [LaporanPenutupanController::class, 'exportExcel'])->name('admin.laporan_penutupan.exportExcel');
    Route::get('laporan_penutupan/exportPdf', [LaporanPenutupanController::class, 'exportPdf'])->name('admin.laporan_penutupan.exportPdf');
    Route::get('laporan_penutupan/exportExcel', [LaporanPenutupanController::class, 'exportExcel'])->name('admin.laporan_penutupan.exportExcel');
    Route::get('/laporan_penutupan/{id}/bukti_visit', [LaporanPenutupanController::class, 'showPenutupanProof'])->name('admin.laporan_penutupan.showPenutupanProof');
    Route::get('/laporan_penutupan/filter', [LaporanPenutupanController::class, 'filter'])->name('admin.laporan_penutupan.filter');

    
    
    Route::get('/laporan_transfer/{id}/bukti_pembayaran', [LaporanTransferController::class, 'showPaymentProof'])->name('admin.laporan_transfer.showPaymentProof');
    Route::get('/laporan_transfer/{id}/bukti_sspd', [LaporanTransferController::class, 'showSspdProof'])->name('admin.laporan_transfer.showSspdProof');
    Route::get('/laporan_transfer/filter', [LaporanTransferController::class, 'filter'])->name('admin.laporan_transfer.filter');
    // Route::post('/laporan_transfer/konfirmasi/{id}', [LaporanTransferController::class, 'konfirmasi'])
    // ->name('admin.laporan_transfer.konfirmasi');
    Route::patch('/laporan-transfer/konfirmasi/{id}', [LaporanTransferController::class, 'updateKonfirmasi'])->name('admin.laporan_transfer.update_konfirmasi');
    Route::patch('/laporan-transfer/konfirmasi/{id}', [LaporanTransferController::class, 'updateKonfirmasi']);

    
    
    Route::get('/laporan_tunai/{id}/bukti_pembayaran', [LaporanTunaiController::class, 'showPaymentProof'])->name('admin.laporan_tunai.showPaymentProof');

    Route::get('/laporan_tunai/{id}/bukti_sspd', [LaporanTunaiController::class, 'showSspdProof'])->name('admin.laporan_tunai.showSspdProof');

    Route::get('/laporan_tunai/filter', [LaporanTunaiController::class, 'filter'])->name('admin.laporan_tunai.filter');
    // Route::post('/laporan_tunai/konfirmasi/{id}', [LaporanTunaiController::class, 'konfirmasi'])
    // ->name('admin.laporan_tunai.konfirmasi');
    Route::patch('/laporan-tunai/konfirmasi/{id}', [LaporanTunaiController::class, 'updateKonfirmasi'])->name('admin.laporan_tunai.update_konfirmasi');
    Route::patch('/laporan-tunai/konfirmasi/{id}', [LaporanTunaiController::class, 'updateKonfirmasi']);


    
    
    




    // JENIS PAJAK
    Route::get('/jenis_pajak', [JenisPajakController::class, 'index'])->name('admin.jenis_pajak.data');
    Route::get('/jenis_pajak/add', [JenisPajakController::class, 'create'])->name('admin.jenis_pajak.create');
    Route::post('/jenis_pajak/add', [JenisPajakController::class, 'store'])->name('admin.jenis_pajak.store');
    Route::get('/jenis_pajak/edit/{id}', [JenisPajakController::class, 'edit'])->name('admin.jenis_pajak.edit');
    Route::put('/jenis_pajak/update/{id}', [JenisPajakController::class, 'update'])->name('admin.jenis_pajak.update');
    Route::delete('/jenis_pajak/delete/{id}', [JenisPajakController::class, 'destroy'])->name('admin.jenis_pajak.delete');

    // PESAN
    Route::get('/kelola_pesan_whatsapp', [KelolaPesanWhatsappController::class, 'index'])->name('admin.kelola_pesan_whatsapp.data');
    Route::get('/kelola_pesan_whatsapp/add', [KelolaPesanWhatsappController::class, 'create'])->name('admin.kelola_pesan_whatsapp.create');
    Route::post('/kelola_pesan_whatsapp/add', [KelolaPesanWhatsappController::class, 'store'])->name('admin.kelola_pesan_whatsapp.store');
    Route::get('/kelola_pesan_whatsapp/edit/{id}', [KelolaPesanWhatsappController::class, 'edit'])->name('admin.kelola_pesan_whatsapp.edit');
    Route::put('/kelola_pesan_whatsapp/update/{id}', [KelolaPesanWhatsappController::class, 'update'])->name('admin.kelola_pesan_whatsapp.update');
    Route::delete('/kelola_pesan_whatsapp/delete/{id}', [KelolaPesanWhatsappController::class, 'destroy'])->name('admin.kelola_pesan_whatsapp.delete');

    // KATEGORI PAJAK
    // Route::get('/kategori_pajak', [KategoriPajakController::class, 'index'])->name('admin.kategori_pajak.data');
    // Route::get('/kategori_pajak/add', [KategoriPajakController::class, 'create'])->name('admin.kategori_pajak.create');
    // Route::post('/kategori_pajak/add', [KategoriPajakController::class, 'store'])->name('admin.kategori_pajak.store');
    // Route::get('/kategori_pajak/edit/{id}', [KategoriPajakController::class, 'edit'])->name('admin.kategori_pajak.edit');
    // Route::put('/kategori_pajak/update/{id}', [KategoriPajakController::class, 'update'])->name('admin.kategori_pajak.update');
    // Route::delete('/kategori_pajak/delete/{id}', [KategoriPajakController::class, 'destroy'])->name('admin.kategori_pajak.delete');
    // Route::get('/kategori_pajak/filter', [KategoriPajakController::class, 'filter'])->name('admin.kategori_pajak.filter');

    

    // USER
    Route::get('/data_user', [DataUserController::class, 'index'])->name('admin.data_user.data');
    Route::get('/data_user/add', [DataUserController::class, 'create'])->name('admin.data_user.create');
    Route::post('/data_user/add', [DataUserController::class, 'store'])->name('admin.data_user.store');
    Route::delete('/data_user/delete/{id}', [DataUserController::class, 'destroy'])->name('admin.data_user.delete');
    Route::get('/data_user/filter', [DataUserController::class, 'filter'])->name('admin.data_user.filter');
    // Route::get('/data_user/edit/{id}', [DataUserController::class, 'edit'])->name('data_user.edit');
    // Route::put('/data_user/update/{id}', [DataUserController::class, 'update'])->name('data_user.update');
    // Route::get('/search-wp', [DataUserController::class, 'search']);
    // Route::get('/data_user/filter_jenis', [DataUserController::class, 'filterJenis'])->name('data_user.filterJenis');
    // Route::post('/data_user/cetak_rekap', [DataUserController::class, 'cetakRekap'])->name('data_user.cetakRekap');



    // PROFIL
Route::get('/profil/admin', [ProfilAdminController::class, 'index'])->name('admin.profil');
Route::put('/profil/admin/update', [ProfilAdminController::class, 'update'])->name('admin.profil.update');
    
    // FONNTE
    // Route::post('data-wajib-pajak/send-whatsapp', [DataWajibPajakController::class, 'sendWhatsApp'])->name('data-wajib-pajak.send-whatsapp');

});



Route::fallback(function () {
    return response(null, 204); // Tidak ada respon
});


    // zonasi
    //     Route::get('/data_zonasi/filter', [DataZonasiController::class, 'filter'])->name('admin.data_zonasi.filter');
    //     Route::get('/data_zonasi', [DataZonasiController::class, 'index'])->name('admin.data_zonasi.data');
    //     Route::get('/data_zonasi/add', [DataZonasiController::class, 'create'])->name('admin.data_zonasi.create');
    //     Route::post('/data_zonasi/add', [DataZonasiController::class, 'store'])->name('admin.data_zonasi.store');
    //     Route::get('/data_zonasi/edit/{id}', [DataZonasiController::class, 'edit'])->name('admin.data_zonasi.edit');
    //     Route::put('/data_zonasi/update/{id}', [DataZonasiController::class, 'update'])->name('admin.data_zonasi.update');
    //     Route::delete('/data_zonasi/delete/{id}', [DataZonasiController::class, 'destroy'])->name('admin.data_zonasi.delete');
    //     Route::get('/search-wp', [DataZonasiController::class, 'search']);
    //     Route::get('/data_zonasi/filter_jenis', [DataZonasiController::class, 'filterJenis'])->name('data_zonasi.filterJenis');
    //     Route::post('/data_zonasi/store', [DataZonasiController::class, 'storeZonasi'])->name('admin.data_zonasi.storeZonasi');
    //     Route::get('/data_zonasi/import', [DataZonasiController::class, 'importFromWajibPajak'])->name('admin.data_zonasi.import');
    //     Route::get('/data_zonasi/show_import', [DataZonasiController::class, 'showImportForm'])->name('admin.data_zonasi.show_import');
    // //     Route::get('/admin/data-zonasi/import', [DataZonasiController::class, 'showImportForm'])->name('admin.data_zonasi.show_import_form');
    // // Route::post('/admin/data-zonasi/import', [DataZonasiController::class, 'importFromWajibPajak'])->name('admin.data_zonasi.import');


    //     Route::post('/data_zonasi/cetak_rekap', [DataZonasiController::class, 'cetakRekap'])->name('admin.data_zonasi.cetakRekap');