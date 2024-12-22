<?php

    namespace App\Http\Controllers;

    use App\Models\DataPenagihan;
    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    // use App\Models\DataPelunasan;
    use App\Models\LaporanTunai;
    use App\Models\KelolaPesanWhatsapp;
    use App\Models\DataTunai;
    use App\Models\User;
    use App\Models\LaporanPelunasan;
    use App\Models\LaporanPiutang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use App\Exports\LaporanTunaiExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\QueryException;
    use App\Exports\DataPiutangExport;
    use App\Services\FonnteService;
    use GuzzleHttp\Client;
    use Barryvdh\DomPDF\Facade\Pdf;
    use Carbon\Carbon;


    
    class LaporanTunaiController extends Controller
    {
        public function index(Request $request)
        {
            $laporanTunai = LaporanTunai::with(['jenisPajak'])
            ->where('metode_pembayaran', 'Tunai')
        ->orderBy('created_at', 'desc')

                ->get();
        
            return view('admin.laporan_tunai.data', compact('laporanTunai'));
        }

        public function showPaymentProof($id)
        {
            $laporanTunai = LaporanTunai::find($id);
            if (!$laporanTunai || !$laporanTunai->buktipembayaran) {
                return abort(404, 'Bukti pembayaran tidak ditemukan.');
            }
                return response()->file(storage_path("app/public/{$laporanTunai->buktipembayaran}"));
    
        }   
        public function showSspdProof($id)
        {
            $laporanTunai = LaporanTunai::find($id);
            if (!$laporanTunai || !$laporanTunai->buktisspd) {
                return abort(404, 'Bukti sspd tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$laporanTunai->buktisspd}"));
    
        }



        public function filter(Request $request)
        {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
                'zona' => 'nullable|integer',
                'bulan' => 'nullable|string|max:15',
                'konfirmasi' => 'nullable|string',
            ]);
        
            $query = LaporanTunai::query();
        
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
                });
            }
        
            if ($request->filled('jenis_pajak_id')) {
                $query->where('jenis_pajak_id', $request->jenis_pajak_id);
            }
        
            if ($request->filled('zona')) {
                $query->where('zona', $request->zona);
            }
        
            if ($request->filled('konfirmasi')) {
                // Pastikan nilai konfirmasi sesuai dengan nilai enum ('Belum kirim' atau 'Sudah kirim')
                $konfirmasi = $request->konfirmasi == '1' ? 'Belum kirim' : 'Sudah kirim';
                $query->where('konfirmasi', $konfirmasi);
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
        
            $laporanTunai = $query->latest()->get();
        
            return view('admin.laporan_tunai.data', compact('laporanTunai'));
        }


        public function updateKonfirmasi(Request $request, $id)
{
    try {
        // Temukan laporan transfer berdasarkan ID
        $laporanTunai = LaporanTunai::findOrFail($id);
        $phoneNumber = $laporanTunai->telepon;

        // Ambil pesan dengan jenis pesan 'Admin' dari tabel kelolapesanwhatsapp
        $jenisPesan = $this->getPesanByJenis('Pengiriman Pesan dari Admin');  // Mengambil pesan dengan jenis Admin

        if ($jenisPesan) {
            // Ambil URL dokumen SSPD berdasarkan buktisspd
            $sspdUrl = $this->getSspdUrl($laporanTunai->buktisspd);
            // $nama = $laporanTunai->nama_pajak;
            // $tanggal = $laporanTunai->tanggal_pembayaran;
            // $jumlah = $laporanTunai->jumlah_pembayaran;
            // $metode = $laporanTunai->metode_pembayaran;
            // $jumlahFormatted = number_format($jumlah, 0, ',', '.');
        
            // Pesan dengan format jumlah pembayaran yang diformat
            $message = "Wajib Pajak Yang Terhormat,\nTerimakasih atas Penyelesaian Piutang Pajak Anda, Berikut ini adalah bukti bayar SSPD anda yang dapat didownload pada sistem kami\n$sspdUrl";
            // $message = $jenisPesan."\n". $sspdUrl;
            // ."\nPesan ini dikirimkan oleh Admin BAPENDA Kota Ambon"
            // $message = $jenisPesan."\n Nama pajak: $nama\nTanggal pembayaran: $tanggal\nJumlah pembayaran: Rp$jumlahFormatted, dengan metode pembayaran $metode\n\nDokumen SSPD anda dapat dilihat melalui link ini, " . $sspdUrl;
            
            $this->sendMessage($phoneNumber, $message);
        } else {
            // Jika tidak ada pesan ditemukan, gunakan pesan default
            $message = 'Terimakasih telah melakukan pembayaran pajak Anda.';
            $this->sendMessage($phoneNumber, $message);
        }

        // Perbarui status konfirmasi pada laporan transfer
        $laporanTunai->konfirmasi = 'Sudah kirim';
        $laporanTunai->save();

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi berhasil diperbarui dan pesan WhatsApp terkirim.',
        ]);
    } catch (\Exception $e) {
        // Tangani jika ada exception yang tidak terduga
        dd([
            'error_message' => $e->getMessage(),
            'trace' => $e->getTrace(),
        ]);
    }
}

public function getSspdUrl($fileName)
{
    // Menentukan path ke file SSPD yang ada di storage
    if ($fileName) {
        return asset('storage/' . $fileName);
    }
    return '';  // Kembalikan string kosong jika tidak ada file
}

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
            return Excel::download(new LaporanTunaiExport, 'laporan_tunai.xlsx');
        }
        
        public function exportPdf(Request $request)
{
    // Mulai query untuk LaporanTunai
    $query = LaporanTunai::query();

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

    // Filter berdasarkan zona
    if ($request->filled('zona')) {
        $query->where('zona', $request->zona);
    }

    // Filter berdasarkan konfirmasi
    if ($request->filled('konfirmasi')) {
        $konfirmasi = $request->konfirmasi == '1' ? 'Belum kirim' : 'Sudah kirim';
        $query->where('konfirmasi', $konfirmasi);
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
    $laporanTunai = $query->latest()->get();

    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('admin.laporan_tunai.pdf', compact('laporanTunai'));

    // Unduh file PDF
    return $pdf->download('laporan_tunai.pdf');
}
    }