<?php

    namespace App\Http\Controllers;

    use App\Models\DataPenagihan;
    use App\Models\DataPenetapan;
    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    use App\Models\User;
    use App\Models\LaporanPelunasan;
    use App\Models\LaporanPiutang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use App\Exports\DataPenagihanExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\QueryException;
    use App\Exports\DataPiutangExport;
    use Barryvdh\DomPDF\Facade\Pdf;
    // use App\Notifications\NewDataPenagihanNotification;
    // use Illuminate\Support\Facades\Notification;
    // validate
    
    class DataPenagihanController extends Controller
    {
        public function index(Request $request)
        {
            $user = auth()->user(); // Ambil data user yang login
            $zonasiUser = $user->pembagian_zonasi; // Misal: zonasi dari atribut user
        
            // Filter data penagihan berdasarkan zonasi user dan status
            $dataPenagihan = DataPenagihan::where('pembagian_zonasi', $zonasiUser)
                ->where('status', 'Belum Bayar')
                ->get();
        
            return view('petugas_penagihan.data', compact('dataPenagihan'));
        }
        
    
        public function createDataPenagihanFromZonasi()
        {
            try {
                // Ambil pembagian zonasi dari petugas yang sedang login
                $petugas = Auth::user();
                $zonasiPetugas = $petugas->pembagian_zonasi;
    
                // Ambil data laporan piutang berdasarkan zonasi petugas
                $laporanPiutang = LaporanPiutang::where('pembagian_zonasi', $zonasiPetugas)->get();
                
                if ($laporanPiutang->isEmpty()) {
                    Log::info("Tidak ada data piutang untuk zonasi: {$zonasiPetugas}");
                    return redirect()->back()->with('error', 'Tidak ada data piutang untuk zonasi ini.');
                }
    
                foreach ($laporanPiutang as $piutang) {
                    // Periksa apakah data piutang sudah ada di DataPenagihan
                    $existingData = DataPenagihan::where('npwpd', $piutang->npwpd)
                        ->where('periode', $piutang->periode)
                        ->where('pembagian_zonasi', $piutang->pembagian_zonasi)
                        ->first();
                
                    if ($existingData) {
                        Log::warning("Data dengan NPWPD {$piutang->npwpd} dan periode {$piutang->periode} sudah ada di DataPenagihan.");
                        continue;
                    }
                
                    // Jika tidak ada, buat data baru
                    DataPenagihan::create([
                        'nama_pajak' => $piutang->nama_pajak,
                        'alamat' => $piutang->alamat,
                        'npwpd' => $piutang->npwpd,
                        'jenis_pajak_id' => $piutang->jenis_pajak_id,
                        'kategori_pajak_id' => $piutang->kategori_pajak_id,
                        'nomor_telepon' => $piutang->nomor_telepon,
                        'pembagian_zonasi' => $piutang->pembagian_zonasi,
                        'jumlah_penagihan' => $piutang->jumlah_penagihan,
                        'periode' => $piutang->periode,
                        'status' => 'Belum Bayar',
                    ]);
                }
                
                Log::info("Data berhasil dipindahkan dari LaporanPiutang ke DataPenagihan.");
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
        
            $namaPetugas = $dataPenagihan->petugasPenagihan->nama ?? 'Tidak Diketahui';
            $zonasi = $dataPenagihan->pembagian_zonasi ?? 'Zonasi Tidak Diketahui';
            $tempatPembayaran = "Petugas Penagihan {$namaPetugas} (Zonasi $zonasi)";
            $tempatPembayaran = substr($tempatPembayaran, 0, 255);
        
            LaporanPelunasan::create([
                'nama_pajak' => $dataPenagihan->nama_pajak,
                'alamat' => $dataPenagihan->alamat,
                'npwpd' => $dataPenagihan->npwpd,
                'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                'kategori_pajak_id' => $dataPenagihan->kategori_pajak_id,
                'nomor_telepon' => $dataPenagihan->nomor_telepon,
                'pembagian_zonasi' => $dataPenagihan->pembagian_zonasi,
                'jumlah_penagihan' => $dataPenagihan->jumlah_penagihan,
                'periode' => $dataPenagihan->periode,
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'buktipembayaran' => $buktiPembayaranPath,
                'buktivisit' => $buktiVisitPath,
                'tempat_pembayaran' => $tempatPembayaran,
            ]);
        
            Log::info('Data berhasil disalin ke LaporanPelunasan.', ['npwpd' => $dataPenagihan->npwpd]);
        
            // Update status di DataPenetapan
            $dataPenetapan = DataPenetapan::where('npwpd', $dataPenagihan->npwpd)->first();
            if ($dataPenetapan) {
                $dataPenetapan->status = 'Sudah Bayar';
                $dataPenetapan->save();
            }
        
            // Hapus data dari LaporanPiutang dan DataPenagihan
            LaporanPiutang::where('npwpd', $dataPenagihan->npwpd)->delete();
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

//     // Hapus data dari LaporanPiutang dan DataPenagihan
//     LaporanPiutang::where('npwpd', $dataPenagihan->npwpd)->delete();
//     $dataPenagihan->delete();

//     return redirect()->route('data_penagihan.data')
//         ->with('success', 'Status berhasil diperbarui dan data berhasil dipindahkan ke laporan pelunasan.');
// }

public function exportExcel()
{
    return Excel::download(new DataPenagihanExport, 'data_penagihan.xlsx');
}

public function exportPdf()
{
    $dataPenagihan = DataPenagihan::orderBy('created_at', 'desc')->get();


    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('petugas_penagihan.pdf', compact('dataPenagihan'));

    // Unduh file PDF
    return $pdf->download('data_penagihan.pdf');
}

// public function exportPDF()
// {
//     $dataPenagihan = DataPenagihan::all(); // Ambil semua data penagihan

//     // Ambil zonasi pertama dari data (jika ada beberapa zonasi, Anda bisa menyesuaikan logikanya)
//     $zonasi = $dataPenagihan->first()->pembagian_zonasi ?? 'Tidak Diketahui'; // Default jika zonasi kosong

//     return view('petugas_penagihan.pdf', compact('dataPenagihan', 'zonasi'));
// }

    }