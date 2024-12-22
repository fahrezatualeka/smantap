<?php

    namespace App\Http\Controllers;

    use App\Models\DataPenagihan;
    use App\Models\DataPenetapan;
    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    use App\Models\User;

    use App\Models\LaporanTransfer;
    use App\Models\LaporanTunai;
    use App\Models\LaporanKonfirmasi;
    use App\Models\LaporanPenutupan;
    // use App\Models\LaporanPelunasan;
    use App\Models\KelolaPesanWhatsapp;

    use App\Models\DataTransfer;
    use App\Models\DataTunai;
    use App\Models\DataKonfirmasi;
    use App\Models\DataPenutupan;
    // use App\Models\DataPelunasan;

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
    use App\Models\DataPiutang;
    use Barryvdh\DomPDF\Facade\Pdf;
    use GuzzleHttp\Client;
    use Carbon\Carbon;

    // use App\Notifications\NewDataPenagihanNotification;
    // use Illuminate\Support\Facades\Notification;
    // validate

    class DataPenagihanController extends Controller
    {
        public function index(Request $request)
        {
            $user = auth()->user(); // Ambil data user yang login
            $zonaUser = $user->zona; // Misal: zonasi dari atribut user
        
            $dataPenagihan = DataPenagihan::where('zona', $zonaUser)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // dd($dataPenagihan); // Memeriksa data yang dikembalikan
        return view('petugas_penagihan.data_penagihan.data', compact('dataPenagihan'));
        }

        public function filter(Request $request)
        {
            // Validasi input
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
                'bulan' => 'nullable|string|max:15',
            ]);
        
            // Ambil zona dari petugas yang sedang login
            $user = auth()->user();
            $zonaPetugas = $user->zona; // Asumsi relasi zona ada di model User
        
            $query = DataPenagihan::query();
        
            // Filter berdasarkan zona petugas
            $query->where('zona', $zonaPetugas);
        
            // Filter berdasarkan pencarian
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
                });
            }
        
            // Filter berdasarkan jenis pajak
            if ($request->filled('jenis_pajak_id')) {
                $query->where('jenis_pajak_id', $request->jenis_pajak_id);
            }
        
            // Filter berdasarkan bulan
            if ($request->filled('bulan')) {
                $bulanMapping = [
                    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 
                    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 
                    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
                ];
        
                $namaBulan = $bulanMapping[$request->bulan] ?? null;
        
                if ($namaBulan) {
                    $query->where('periode', 'LIKE', $namaBulan . '%');
                }
            }
        
            // Ambil data yang difilter
            $dataPenagihan = $query->latest()->get();
        
            return view('petugas_penagihan.data_penagihan.data', compact('dataPenagihan'));
        }

        
    
        public function createDataPenagihanFromZonasi()
        {
            try {
                $petugas = Auth::user();
                $zonaPetugas = $petugas->zona;
        
                $dataPiutang = DataPiutang::where('zona', $zonaPetugas)->get();
        
                if ($dataPiutang->isEmpty()) {
                    return redirect()->back()->with('error', 'Tidak ada data piutang untuk zonasi ini.');
                }
        
                foreach ($dataPiutang as $piutang) {
                    DataPenagihan::updateOrCreate(
                        ['npwpd' => $piutang->npwpd], // Hanya NPWPD yang unik
                        $piutang->only(['nama_pajak', 'alamat', 'npwpd', 'jenis_pajak_id', 'telepon', 'zona', 'periode'])
                    );
                }
        
                return redirect()->back()->with('success', 'Data berhasil dipindahkan ke Data Penagihan!');
            } catch (\Exception $e) {
                Log::error("Gagal memindahkan data piutang ke penagihan: " . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memindahkan data.');
            }
        }

        
        
        
        public function markAsPaid(Request $request, $id)
        {
            $dataPenagihan = DataPenagihan::findOrFail($id);

    // Validasi umum
    $validated = $request->validate([
        'metode_pembayaran' => 'required',
        // 'metode_pembayaran' => 'required|in:Transfer,Tunai,Konfirmasi,Penutupan',
        'keterangan' => 'nullable',
    ]);

    // Validasi metode pembayaran
    // $metodePembayaran = $request->metode_pembayaran;
    $metodePembayaran = $validated['metode_pembayaran']; 
    if ($metodePembayaran === 'Transfer' || $metodePembayaran === 'Tunai') {
        $request->validate([
            'jumlah_pembayaran' => 'nullable|numeric',
            'buktipembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    } elseif ($metodePembayaran === 'Konfirmasi') {
        $request->validate([
            'buktivisit' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    } elseif ($metodePembayaran === 'Penutupan') {
        $request->validate([
            'buktipenutupan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    }

    $jumlahPembayaran = $request->input('jumlah_pembayaran', 0); // Ambil jumlah_pembayaran dari request, default 0 jika tidak ada

    // Simpan bukti pembayaran (jika ada)
    $buktiPembayaranPath = null;
    if ($request->filled('foto_result_pembayaran')) {
        $fotoBase64 = $request->input('foto_result_pembayaran');
        if (preg_match('/^data:image\/\w+;base64,/', $fotoBase64)) {
            $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64));
            $fileName = 'foto_pembayaran_' . time() . '.png';
            $filePath = 'uploads/pembayaran/' . $fileName;

            Storage::disk('public')->put($filePath, $image);
            $buktiPembayaranPath = $filePath;
        } else {
            Log::error('Foto pembayaran tidak valid atau tidak dalam format Base64');
        }
    }

    // Simpan bukti visit (jika ada)
    $buktiVisitPath = null;
    if ($request->filled('foto_result_visit')) {
        $fotoBase64 = $request->input('foto_result_visit');
        if (preg_match('/^data:image\/\w+;base64,/', $fotoBase64)) {
            $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64));
            $fileName = 'foto_visit_' . time() . '.png';
            $filePath = 'uploads/visit/' . $fileName;

            Storage::disk('public')->put($filePath, $image);
            $buktiVisitPath = $filePath;
        } else {
            Log::error('Foto visit tidak valid atau tidak dalam format Base64');
        }
    }

    // Simpan bukti penutupan (jika ada)
    $buktiPenutupanPath = null;
    if ($request->filled('foto_result_penutupan')) {
        $fotoBase64 = $request->input('foto_result_penutupan');
        if (preg_match('/^data:image\/\w+;base64,/', $fotoBase64)) {
            $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64));
            $fileName = 'foto_penutupan_' . time() . '.png';
            $filePath = 'uploads/penutupan/' . $fileName;

            Storage::disk('public')->put($filePath, $image);
            $buktiPenutupanPath = $filePath;
        } else {
            Log::error('Foto penutupan tidak valid atau tidak dalam format Base64');
        }
    }

    // Simpan ke database jika path tersedia
    $dataPenagihan->jumlah_pembayaran = $jumlahPembayaran;
    if ($buktiPembayaranPath) {
        $dataPenagihan->buktipembayaran = $buktiPembayaranPath;
    }
    if ($buktiVisitPath) {
        $dataPenagihan->buktivisit = $buktiVisitPath;
    }
    if ($buktiPenutupanPath) {
        $dataPenagihan->buktipenutupan = $buktiPenutupanPath;
    }

    $dataPenagihan->status = 'Sudah Bayar';
    $dataPenagihan->save();
        

                    
                
                    // $jumlahPembayaran = $request->input('jumlah_pembayaran');
                    // if (in_array($metodePembayaran, ['Transfer', 'Tunai'])) {
                    //     $dataPenagihan->save();
                    // }

                    $namaPetugas = $dataPenagihan->petugasPenagihan->nama ?? 'Tidak Diketahui';
                    $zonasi = $dataPenagihan->zona ?? 'Zonasi Tidak Diketahui';
                    $pengirim = "Petugas {$namaPetugas} (Zona $zonasi)";
                    $pengirim = substr($pengirim, 0, 255);

// $jenisPesan = $this->getPesanByJenis('Pengiriman Pesan dari Petugas Penagihan');
// $metodePembayaran = $request->input('metode_pembayaran');

// $tanggalFormatted = Carbon::now();
// $tanggal = Carbon::parse($tanggalFormatted)->locale('id')->isoFormat('D MMMM YYYY');
// // $jumlahPembayaran = $request->has('jumlah_pembayaran') ? intval($request->input('jumlah_pembayaran')) : null; // Pastikan nilai adalah integer
// $metode = $request->has('metode_pembayaran') ? ($request->input('metode_pembayaran')) : null;

// $phoneNumber = $dataPenagihan->telepon;
//             $nama = $dataPenagihan->nama_pajak;
//             $alamat = $dataPenagihan->alamat;
//             $npwpd = $dataPenagihan->npwpd;
//             $jenis = $dataPenagihan->jenisPajak->jenispajak;
//             $periode = $dataPenagihan->periode;

// if ($metodePembayaran === 'Transfer' || $metodePembayaran === 'Tunai') {
//     $message = "Wajib Pajak Yang Terhormat,\n\nTerimakasih atas kerjasama nya. Sistem kami telah mendeteksi yang dititipkan pada petugas Kami untuk melanjutkan transaksi pajak $periode pada loket BAPENDA Kota Ambon.\nKami akan membantu melanjutkan transaksi pajak anda hingga penerbitan bukti bayar SSPD.\n\nTerimakasih.";
//     // $message = $jenisPesan."\n Nama pajak: $nama\nAlamat: $alamat\n NPWPD: $npwpd\n Jenis pajak: $jenis\n Periode piutang pajak: $periode\nTanggal pembayaran: $tanggal\nJumlah Pembayaran: Rp" . number_format($jumlahPembayaran, 0, ',', '.')."\nMetode Pembayaran: $metode";
// } elseif ($metodePembayaran === 'Konfirmasi') {
//     $message = "Wajib Pajak Yang Terhormat, pembayaran pajak anda ditunda untuk dilakukan konfirmasi ulang. Mohon segera menyelesaikan pembayaran pajak Anda.";
// } else {
//     $message = "Terima kasih atas partisipasi Anda dalam pembayaran pajak.";
// }

// $this->sendMessage($phoneNumber, $message);
        
            if ($metodePembayaran === 'Transfer') {
                LaporanTransfer::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_pembayaran' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'jumlah_pembayaran' => $jumlahPembayaran,
                    'buktipembayaran' => $buktiPembayaranPath,
                    'keterangan' => $validated['keterangan'],
                    'pengirim' => $pengirim,
                ]);
                DataTransfer::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_pembayaran' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'jumlah_pembayaran' => $jumlahPembayaran,
                    'buktipembayaran' => $buktiPembayaranPath,
                    'keterangan' => $validated['keterangan'],
                ]);
            } elseif ($metodePembayaran === 'Tunai') {
                LaporanTunai::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_pembayaran' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'jumlah_pembayaran' => $jumlahPembayaran,
                    'buktipembayaran' => $buktiPembayaranPath,
                    // 'buktisspd' => $buktiSspdPath,
                    'keterangan' => $validated['keterangan'],
                    'pengirim' => $pengirim,
                ]);
                DataTunai::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_pembayaran' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'jumlah_pembayaran' => $jumlahPembayaran,
                    'buktipembayaran' => $buktiPembayaranPath,
                    // 'buktisspd' => $buktiSspdPath,
                    'keterangan' => $validated['keterangan'],
                ]);
            } elseif ($metodePembayaran === 'Konfirmasi') {
                LaporanKonfirmasi::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_kunjungan' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'buktivisit' => $buktiVisitPath,
                    'keterangan' => $validated['keterangan'],
                    'pengirim' => $pengirim,
                ]);
                DataKonfirmasi::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_kunjungan' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'buktivisit' => $buktiVisitPath,
                    'keterangan' => $validated['keterangan'],
                ]);
            } elseif ($metodePembayaran === 'Penutupan') {
                LaporanPenutupan::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_kunjungan' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'buktipenutupan' => $buktiPenutupanPath,
                    'keterangan' => $validated['keterangan'],
                    'pengirim' => $pengirim,
                ]);
                DataPenutupan::create([
                    'nama_pajak' => $dataPenagihan->nama_pajak,
                    'alamat' => $dataPenagihan->alamat,
                    'npwpd' => $dataPenagihan->npwpd,
                    'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
                    'telepon' => $dataPenagihan->telepon,
                    'zona' => $dataPenagihan->zona,
                    'periode' => $dataPenagihan->periode,
                    'tanggal_kunjungan' => now(),
                    'metode_pembayaran' => $metodePembayaran,
                    'buktipenutupan' => $buktiPenutupanPath,
                    'keterangan' => $validated['keterangan'],
                ]);
            }
        
            $dataPiutang = DataPiutang::where('npwpd', $dataPenagihan->npwpd)->first();
            if ($dataPiutang) {
                $dataPiutang->delete();
            }

            $dataPenagihan->delete();
        
            return redirect()->route('petugas_penagihan.data_penagihan.data')
                ->with('success', 'Status berhasil diperbarui dan data berhasil dipindahkan.');
        }





// public function markAsPaid(Request $request, $id)
//         {
//             $dataPenagihan = DataPenagihan::findOrFail($id);

//             $validated = $request->validate([
//                 'metode_pembayaran' => 'required|string',
//                 'keterangan' => 'nullable|string',
//                 'foto_result' => 'nullable|string',
//             ]);

//             // Inisialisasi path untuk bukti pembayaran dan visit
//             $buktiPembayaranPath = null;
//             $buktiVisitPath = null;

//             // Proses simpan foto hasil visit

//             if ($request->filled('foto_result')) {
//                 try {
//                     $fotoBase64 = $request->input('foto_result');
//                     $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64));
//                     $fileName = 'foto_' . time() . '.png';
//                     $filePath = 'uploads/visit/' . $fileName;
//                     Storage::disk('public')->put($filePath, $image);
//                     $buktiVisitPath = $filePath;
//                 } catch (\Exception $e) {
//                     return back()->withErrors(['foto_result' => 'Gagal menyimpan foto. Format tidak valid.']);
//                 }
//             }

//             // Simpan data dalam transaksi
//             DB::transaction(function () use ($dataPenagihan, $validated, $buktiVisitPath) {
//                 $dataPenagihan->update([
//                     'status' => 'Sudah Bayar',
//                     'buktivisit' => $buktiVisitPath,
//                 ]);

//                 // Simpan ke tabel LaporanKonfirmasi
//                 LaporanKonfirmasi::create([
//                     'nama_pajak' => $dataPenagihan->nama_pajak,
//                     'alamat' => $dataPenagihan->alamat,
//                     'npwpd' => $dataPenagihan->npwpd,
//                     'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
//                     'telepon' => $dataPenagihan->telepon,
//                     'zona' => $dataPenagihan->zona,
//                     'periode' => $dataPenagihan->periode,
//                     'tanggal_pembayaran' => now(),
//                     'metode_pembayaran' => $validated['metode_pembayaran'],
//                     'buktivisit' => $buktiVisitPath,
//                     'keterangan' => $validated['keterangan'],
//                 ]);

//                 // Simpan ke tabel DataKonfirmasi
//                 DataKonfirmasi::create([
//                     'nama_pajak' => $dataPenagihan->nama_pajak,
//                     'alamat' => $dataPenagihan->alamat,
//                     'npwpd' => $dataPenagihan->npwpd,
//                     'jenis_pajak_id' => $dataPenagihan->jenis_pajak_id,
//                     'telepon' => $dataPenagihan->telepon,
//                     'zona' => $dataPenagihan->zona,
//                     'periode' => $dataPenagihan->periode,
//                     'tanggal_pembayaran' => now(),
//                     'metode_pembayaran' => $validated['metode_pembayaran'],
//                     'buktivisit' => $buktiVisitPath,
//                     'keterangan' => $validated['keterangan'],
//                 ]);
//             });

//             return redirect()->route('petugas_penagihan.data_penagihan.data')
//             ->with('success', 'Status berhasil diperbarui dan data berhasil      disimpan.');
//         }



        public function getPesanByJenis($jenisPesan)
{
    // Ambil pesan dari tabel kelolapesanwhatsapp berdasarkan jenis pesan
    $pesan = KelolaPesanWhatsapp::where('jenis_pesan', $jenisPesan)->first();

    if ($pesan) {
        return $pesan->deskripsi;
    }

    // Jika tidak ada pesan ditemukan, kembalikan pesan default
    return null;
}



public function sendMessage($phoneNumber, $message)
{
    $apiUrl = 'https://api.fonnte.com/send';
    $apiKey = 'NehkJetr9zN3JaXXXqJb';  // Gantilah dengan API key Anda
    
    $content = $message;

    $client = new Client();
    try {
        $response = $client->post($apiUrl, [
            'headers' => [
                'Authorization' => $apiKey,
            ],
            'multipart' => [
                [
                    'name' => 'target',
                    'contents' => $phoneNumber,
                ],
                [
                    'name' => 'message',
                    'contents' => $content,
                ],
                [
                    'name' => 'schedule',
                    'contents' => 0,
                ],
                [
                    'name' => 'typing',
                    'contents' => false,
                ],
                [
                    'name' => 'delay',
                    'contents' => 2,
                ],
                [
                    'name' => 'countryCode',
                    'contents' => '62',  // Gantilah dengan kode negara yang sesuai
                ],
            ],
        ]);

        $status = json_decode($response->getBody()->getContents(), true);
        Log::info('Fonnte API Response:', $status);

        if (isset($status['status']) && $status['status'] == 'success') {
            // Sukses mengirim pesan
            return true;
        } else {
            // Gagal mengirim pesan
            Log::error('Fonnte API Error:', ['message' => $status['message'] ?? 'Tidak ada pesan error.']);
            return false;
        }
    } catch (\Exception $e) {
        // Log error jika ada exception
        Log::error('Fonnte API Error:', ['message' => $e->getMessage()]);
        return false;
    }
}

public function exportExcel()
{
    return Excel::download(new DataPenagihanExport, 'petugas_penagihan.data_penagihan.xlsx');
}

    public function exportPdf(Request $request)
    {
        $user = auth()->user(); // Ambil data user yang login
        $zonaUser = $user->zona; // Zonasi dari user yang login

        // Periksa apakah ada filter yang diterapkan pada data
        $query = DataPenagihan::where('zona', $zonaUser)->orderBy('created_at', 'desc');
        
        // Filter berdasarkan request yang dikirimkan untuk export
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
                ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
            });
        }
        if ($request->filled('jenis_pajak_id')) {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
        if ($request->filled('bulan')) {
            $bulanMapping = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
            ];
            $namaBulan = $bulanMapping[$request->bulan] ?? null;

            if ($namaBulan) {
                $query->where('periode', 'LIKE', $namaBulan . '%');
            }
        }

        // Ambil data yang telah difilter dan ditampilkan
        $dataPenagihan = $query->get();

        // Kirim data ke view khusus untuk PDF
        $pdf = Pdf::loadView('petugas_penagihan.data_penagihan.pdf', compact('dataPenagihan'));

        // Unduh file PDF
        return $pdf->download('data_penagihan_petugas.pdf');
    }
}