<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Exports\WpExport;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


use App\Http\Controllers\Auth\LoginController;

// PETUGAS PENAGIHAN
use App\Http\Controllers\IndexPetugasPenagihanController;
use App\Http\Controllers\DataPenagihanController;
use App\Http\Controllers\BuktiController;

// ADMIN
use App\Http\Controllers\IndexAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataWajibPajakController;
use App\Http\Controllers\DataZonasiController;
use App\Http\Controllers\DataPenetapanController;
use App\Http\Controllers\DataPiutangController;
use App\Http\Controllers\LaporanPenagihanController;
use App\Http\Controllers\LaporanPiutangController;
use App\Http\Controllers\LaporanPelunasanController;
use App\Http\Controllers\DataUserController;
use App\Http\Controllers\JenisPajakController;
use App\Http\Controllers\KategoriPajakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FonnteController;
use App\Models\LaporanPelunasan;

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

// LOGIN
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/proses', [LoginController::class, 'login'])->name('proses.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// PIMPINAN
Route::group(['middleware' => ['role:pimpinan']], function () {
    Route::get('/pimpinan', [IndexAdminController::class, 'index'])->name('pimpinan.index');
    Route::get('/dashboard_pimpinan', [DashboardController::class, 'index'])->name('dashboard');
    // profil pimpinan
    Route::get('/profil/pimpinan', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/pimpinan/update', [ProfileController::class, 'update'])->name('profile.update');
});

// PETUGAS PENAGIHAN
Route::group(['middleware' => ['role:petugas_penagihan']], function () {
    Route::get('/petugas_penagihan', [IndexAdminController::class, 'index'])->name('petugas_penagihan.index');
    Route::get('/data_penagihan', [DataPenagihanController::class, 'index'])->name('data_penagihan.data');
    Route::post('/data_penagihan/uploadBuktiPembayaran/{id}', [DataPenagihanController::class, 'uploadBuktiPembayaran'])->name('data_penagihan.uploadBuktiPembayaran');
    Route::post('/data_penagihan/markAsPaid/{id}', [DataPenagihanController::class, 'markAsPaid'])->name('datapenagihan.markAsPaid');
    Route::delete('/datapenagihan/{id}', [DataPenagihanController::class, 'deleteDatapenagihan'])->name('datapenagihan.delete');
    // Route::put('/data_penagihan/{id}/update-status', [DataPenagihanController::class, 'updateStatus'])->name('data_penagihan.updateStatus');

// profil petugas penagihan
Route::get('/profil/petugas_penagihan', [ProfileController::class, 'index'])->name('profile');
Route::post('/profile/petugas_penagihan/update', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/data_penagihan/upload/{id}', [DataPenagihanController::class, 'uploadBuktiPembayaran'])->name('data_penagihan.upload');
// Route::patch('data_penagihan/{id}/update-status', [DataPenagihanController::class, 'markAsPaid'])->name('data_penagihan.updateStatus');
Route::put('/data_penagihan/{id}/update-status', [DataPenagihanController::class, 'markAsPaid'])->name('data_penagihan.updateStatus');



    Route::get('/mark-as-read/{id}', function ($id) {
        auth()->user()->notifications->where('id', $id)->markAsRead();
        return redirect()->back();
    })->name('markAsRead');
});



// ADMIN
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
    Route::get('/data_penetapan/filter', [DataPenetapanController::class, 'filter'])->name('admin.data_penetapan.filter');
    Route::get('/data_penetapan', [DataPenetapanController::class, 'index'])->name('admin.data_penetapan.data');
    Route::post('/data_penetapan/import', [DataPenetapanController::class, 'import'])->name('admin.data_penetapan.import');
    // Route::get('/data_penetapan/index', [DataPenetapanController::class, 'index'])->name('admin.data_penetapan.index');
    Route::get('/data_penetapan/add', [DataPenetapanController::class, 'create'])->name('admin.data_penetapan.create');
    Route::post('/data_penetapan/add', [DataPenetapanController::class, 'store'])->name('admin.data_penetapan.store');
    Route::get('/data_penetapan/edit/{id}', [DataPenetapanController::class, 'edit'])->name('admin.data_penetapan.edit');
    Route::put('/data_penetapan/update/{id}', [DataPenetapanController::class, 'update'])->name('admin.data_penetapan.update');
    Route::delete('/data_penetapan/delete/{id}', [DataPenetapanController::class, 'destroy'])->name('admin.data_penetapan.delete');
    // Route::put('/data_penetapan/{id}/status', [DataPenetapanController::class, 'updateStatus'])->name('admin.data_penetapan.updateStatus');
    Route::get('/pembayaran', [DataPenetapanController::class, 'pembayaran'])->name('admin.data_penetapan.pembayaran');
    // Route::get('/pembayaran', [DataPenetapanController::class, 'synchronizeToPiutang'])->name('admin.data_penetapan.synchronizeToPiutang');
    Route::put('/data-penetapan/{id}/update-status', [DataPenetapanController::class, 'updateStatus'])->name('admin.data_penetapan.updateStatus');
    Route::get('data_penetapan/exportExcel', [DataPenetapanController::class, 'exportExcel'])->name('admin.data_penetapan.exportExcel');
    Route::get('data_penetapan/exportPdf', [DataPenetapanController::class, 'exportPdf'])->name('admin.data_penetapan.exportPdf');



    // PIUTANG
    // Route::get('/data_wajibpajak/{npwpd}', [DataWajibPajakController::class, 'getDataByNpwpd']);
    Route::get('/data_piutang/filter', [DataPiutangController::class, 'filter'])->name('admin.data_piutang.filter');
    Route::get('/data_piutang', [DataPiutangController::class, 'index'])->name('admin.data_piutang.data');
    // Route::post('/data_piutang/import', [DataPiutangController::class, 'import'])->name('admin.data_piutang.import');
    Route::post('data_piutang/zonasi', [DataPiutangController::class, 'saveZonasi'])->name('admin.data_piutang.saveZonasi');
    Route::post('/data_piutang/import', [DataPiutangController::class, 'importFromDataPenetapan'])->name('admin.data_piutang.import');
    Route::get('/data_piutang/add', [DataPiutangController::class, 'create'])->name('admin.data_piutang.create');
    Route::post('/data_piutang/add', [DataPiutangController::class, 'store'])->name('admin.data_piutang.store');
    Route::get('/data_piutang/edit/{id}', [DataPiutangController::class, 'edit'])->name('admin.data_piutang.edit');
    Route::put('/data_piutang/update/{id}', [DataPiutangController::class, 'update'])->name('admin.data_piutang.update');
    Route::delete('/data_piutang/delete/{id}', [DataPiutangController::class, 'destroy'])->name('admin.data_piutang.delete');
    Route::get('data_piutang/exportExcel', [DataPiutangController::class, 'exportExcel'])->name('admin.data_piutang.exportExcel');
    Route::get('data_piutang/exportPdf', [DataPiutangController::class, 'exportPdf'])->name('admin.data_piutang.exportPdf');



    // LAPORAN
    // PIUTANG
    Route::get('/laporan_piutang', [LaporanPiutangController::class, 'index'])->name('admin.laporan_piutang.data');
    Route::get('/laporan_piutang/filter', [LaporanPiutangController::class, 'filter'])->name('admin.laporan_piutang.filter');



    // PELUNASAN
    Route::get('/laporan_pelunasan', [LaporanPelunasanController::class, 'index'])->name('admin.laporan_pelunasan.data');
    Route::get('/laporan_pelunasan/filter', [LaporanPelunasanController::class, 'filter'])->name('admin.laporan_pelunasan.filter');
    Route::get('/laporan_pelunasan/{id}/bukti_pembayaran', [LaporanPelunasanController::class, 'showPaymentProof'])->name('admin.laporan_pelunasan.showPaymentProof');
    Route::get('/laporan_pelunasan/{id}/bukti_visit', [LaporanPelunasanController::class, 'showVisitProof'])->name('admin.laporan_pelunasan.showVisitProof');
    Route::get('laporan_pelunasan/exportExcel', [LaporanPelunasanController::class, 'exportExcel'])->name('admin.laporan_pelunasan.exportExcel');
    Route::get('laporan_pelunasan/exportPdf', [LaporanPelunasanController::class, 'exportPdf'])->name('admin.laporan_pelunasan.exportPdf');



    // JENIS PAJAK
    Route::get('/jenis_pajak', [JenisPajakController::class, 'index'])->name('admin.jenis_pajak.data');
    Route::get('/jenis_pajak/add', [JenisPajakController::class, 'create'])->name('admin.jenis_pajak.create');
    Route::post('/jenis_pajak/add', [JenisPajakController::class, 'store'])->name('admin.jenis_pajak.store');
    Route::get('/jenis_pajak/edit/{id}', [JenisPajakController::class, 'edit'])->name('admin.jenis_pajak.edit');
    Route::put('/jenis_pajak/update/{id}', [JenisPajakController::class, 'update'])->name('admin.jenis_pajak.update');
    Route::delete('/jenis_pajak/delete/{id}', [JenisPajakController::class, 'destroy'])->name('admin.jenis_pajak.delete');



    // KATEGORI PAJAK
    Route::get('/kategori_pajak', [KategoriPajakController::class, 'index'])->name('admin.kategori_pajak.data');
    Route::get('/kategori_pajak/add', [KategoriPajakController::class, 'create'])->name('admin.kategori_pajak.create');
    Route::post('/kategori_pajak/add', [KategoriPajakController::class, 'store'])->name('admin.kategori_pajak.store');
    Route::get('/kategori_pajak/edit/{id}', [KategoriPajakController::class, 'edit'])->name('admin.kategori_pajak.edit');
    Route::put('/kategori_pajak/update/{id}', [KategoriPajakController::class, 'update'])->name('admin.kategori_pajak.update');
    Route::delete('/kategori_pajak/delete/{id}', [KategoriPajakController::class, 'destroy'])->name('admin.kategori_pajak.delete');
    Route::get('/kategori_pajak/filter', [KategoriPajakController::class, 'filter'])->name('admin.kategori_pajak.filter');



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
    Route::get('/profil/admin', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/admin/update', [ProfileController::class, 'update'])->name('profile.update');
    
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

    // laporan
    // Route::get('/laporan_penagihan', [LaporanPenagihanController::class, 'index'])->name('admin.laporan_penagihan.data');
    // Route::post('/laporan_penagihan/markAsPaid/{id}', [LaporanPenagihanController::class, 'markAsPaid'])->name('admin.laporanpenagihan.markAsPaid');
    // Route::get('/laporan_penagihan', [LaporanPenagihanController::class, 'filter'])->name('admin.laporan_penagihan.filter');
    // Route::get('/laporan_penagihan/{id}/bukti_visit', [LaporanPenagihanController::class, 'showVisitProof'])->name('admin.laporan_penagihan.showVisitProof');
    // Route::get('/laporan_penagihan/{id}/bukti_pembayaran', [LaporanPenagihanController::class, 'showPaymentProof'])->name('admin.laporan_penagihan.showPaymentProof');
    // Route::get('/laporan_penagihan', [LaporanPenagihanController::class, 'filter'])->name('admin.laporan_penagihan.filter');