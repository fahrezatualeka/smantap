<?php

    namespace App\Http\Controllers;

    use App\Models\DataPenagihan;
    use App\Models\DataPenetapan;
    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    use App\Models\User;
    use App\Models\LaporanPelunasan;
    use App\Models\DataPiutang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    // use App\Notifications\NewDataPenagihanNotification;
    // use Illuminate\Support\Facades\Notification;
    // validate
    
    class DataPenagihanController extends Controller
    {
        public function index()
        {
            // Ambil data petugas yang sedang login
            $petugas = Auth::user();
            $zonasiPetugas = $petugas->pembagian_zonasi; // Ambil pembagian zonasi dari petugas
        
            Log::info("Petugas dengan ID {$petugas->id} mengakses data untuk zonasi: {$zonasiPetugas}");
        
            if (!$zonasiPetugas) {
                return redirect()->back()->with('error', 'Zonasi tidak ditemukan untuk petugas ini.');
            }
        
            $dataPenagihan = DataPenagihan::with(['jenisPajak', 'kategoriPajak'])
                ->where('pembagian_zonasi', $zonasiPetugas) // Filter berdasarkan pembagian zonasi
                ->get();
        
            if ($dataPenagihan->isEmpty()) {
                Log::info("Tidak ada data penagihan untuk zonasi: {$zonasiPetugas}");
            }
        
            return view('petugas_penagihan.data', compact('dataPenagihan'));
        }
        
        public function createDataPenagihanFromZonasi($zonasiPetugas)
        {
            try {
                $dataPiutang = DataPiutang::where('pembagian_zonasi', $zonasiPetugas)->get();
                
                if ($dataPiutang->isEmpty()) {
                    Log::info("Tidak ada data piutang untuk zonasi: {$zonasiPetugas}");
                    return redirect()->back()->with('error', 'Tidak ada data piutang untuk zonasi ini.');
                }
        
                foreach ($dataPiutang as $piutang) {
                    if (!$piutang->npwpd || !$piutang->periode) {
                        Log::error("Data piutang tidak lengkap: ", ['piutang' => $piutang]);
                        continue;
                    }
                
                    DataPenagihan::create([
                        'npwpd' => $piutang->npwpd,
                        'nama_pajak' => $piutang->nama_pajak,
                        'alamat' => $piutang->alamat,
                        'jenis_pajak_id' => $piutang->jenis_pajak_id,
                        'kategori_pajak_id' => $piutang->kategori_pajak_id,
                        'nomor_telepon' => $piutang->nomor_telepon,
                        'pembagian_zonasi' => $piutang->pembagian_zonasi,
                        'jumlah_penagihan' => $piutang->jumlah_penagihan,
                        'periode' => $piutang->periode,
                        'status' => 'Belum Bayar',
                    ]);
                }
                
        
                Log::info("Data berhasil dipindahkan dari DataPiutang ke DataPenagihan.");
                return redirect()->back()->with('success', 'Data berhasil dipindahkan ke Data Penagihan!');
            } catch (\Exception $e) {
                Log::error("Gagal memindahkan data piutang ke penagihan: " . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memindahkan data.');
            }
        }

public function markAsPaid(Request $request, $id)
{
    $dataPenagihan = DataPenagihan::findOrFail($id);

    $validated = $request->validate([
        // 'jumlah_pembayaran' => 'required|numeric',
        'tanggal_pembayaran' => 'required|date',
        'buktipembayaran' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
        'buktivisit' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
    ]);

    // Proses upload file
    $buktiPembayaranPath = $request->file('buktipembayaran')->store('uploads/pembayaran', 'public');
    $buktiVisitPath = $request->file('buktivisit')->store('uploads/visit', 'public');

    // Update status di DataPenagihan
    $dataPenagihan->status = 'Sudah Bayar';
    $dataPenagihan->buktipembayaran = $buktiPembayaranPath;
    $dataPenagihan->buktivisit = $buktiVisitPath;
    $dataPenagihan->save();

    LaporanPelunasan::create([
        'nama_pajak' => $dataPenagihan->nama_pajak,
        'alamat' => $dataPenagihan->alamat,
        'npwpd' => $dataPenagihan->npwpd,
        'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
        'kategori_pajak_id' => $dataPenagihan->kategori_pajak_id,
        'jumlah_penagihan' => $dataPenagihan->jumlah_penagihan,
        // 'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
        'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
        'buktipembayaran' => $buktiPembayaranPath,
        'buktivisit' => $buktiVisitPath,
        'tempat_pembayaran' => 'Petugas Penagihan', // Tempat pembayaran untuk DataPenagihan
    ]);
    
    Log::info('Data berhasil disalin ke LaporanPelunasan.', ['npwpd' => $dataPenagihan->npwpd]);
    

    // Update status di DataPenetapan
    $dataPenetapan = DataPenetapan::where('npwpd', $dataPenagihan->npwpd)->first();
    if ($dataPenetapan) {
        $dataPenetapan->status = 'Sudah Bayar';
        $dataPenetapan->save();
    }

    // Hapus data dari DataPiutang dan DataPenagihan
    DataPiutang::where('npwpd', $dataPenagihan->npwpd)->delete();
    $dataPenagihan->delete();

    return redirect()->route('data_penagihan.data')
        ->with('success', 'Status berhasil diperbarui dan data berhasil dipindahkan ke laporan pelunasan.');
}



// PROSES LAPORANPELUNASAN AGAR TIDAK TERJADI DUPLIKAT
// public function markAsPaid(Request $request, $id)
// {
//     $dataPenagihan = DataPenagihan::findOrFail($id);

//     $validated = $request->validate([
//         'jumlah_pembayaran' => 'required|numeric',
//         'tanggal_pembayaran' => 'required|date',
//         'buktipembayaran' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
//         'buktivisit' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
//     ]);

//     // Proses upload file
//     $buktiPembayaranPath = $request->file('buktipembayaran')->store('uploads/pembayaran', 'public');
//     $buktiVisitPath = $request->file('buktivisit')->store('uploads/visit', 'public');

//     // Update status di DataPenagihan
//     $dataPenagihan->status = 'Sudah Bayar';
//     $dataPenagihan->buktipembayaran = $buktiPembayaranPath;
//     $dataPenagihan->buktivisit = $buktiVisitPath;
//     $dataPenagihan->save();

//     LaporanPelunasan::create([
//         'nama_pajak' => $dataPenagihan->nama_pajak,
//         'alamat' => $dataPenagihan->alamat,
//         'npwpd' => $dataPenagihan->npwpd,
//         'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
//         'kategori_pajak_id' => $dataPenagihan->kategori_pajak_id,
//         'jumlah_penagihan' => $dataPenagihan->jumlah_penagihan,
//         'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
//         'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
//         'buktipembayaran' => $buktiPembayaranPath,
//         'buktivisit' => $buktiVisitPath,
//         'tempat_pembayaran' => 'Petugas Penagihan',
//     ]);
    
//     Log::info('Data berhasil disalin ke LaporanPelunasan.', ['npwpd' => $dataPenagihan->npwpd]);

//     // Update status di DataPenetapan
//     $dataPenetapan = DataPenetapan::where('npwpd', $dataPenagihan->npwpd)->first();
//     if ($dataPenetapan) {
//         $dataPenetapan->status = 'Sudah Bayar';
//         $dataPenetapan->save();
//     }

//     // Hapus data dari DataPiutang dan DataPenagihan
//     DataPiutang::where('npwpd', $dataPenagihan->npwpd)->delete();
//     $dataPenagihan->delete();

//     return redirect()->route('data_penagihan.data')
//         ->with('success', 'Status berhasil diperbarui dan data berhasil dipindahkan ke laporan pelunasan.');
// }




    }