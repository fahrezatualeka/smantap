<?php

namespace App\Http\Controllers;

use App\Models\DataWajibPajak;
use App\Models\DataZonasi;
use App\Models\DataPenagihan;
use App\Models\JenisPajak;
use App\Models\LaporanPenagihan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use Illuminate\Support\Facades\DB;

class DataZonasiController extends Controller
{
    // Fungsi untuk menampilkan halaman Zonasi
    public function index()
    {
        $data = DataZonasi::all();
        $petugasPenagihan = User::where('role', 'petugas_penagihan')->get();
        $jenisPajakList = JenisPajak::all();  // Menambahkan variabel jenisPajakList
        
        return view('admin.data_zonasi.data', compact('data', 'petugasPenagihan', 'jenisPajakList'));
    }
    


    public function store(Request $request)
    {
        // Validasi input dari form
        $validatedData = $request->validate([
            'zonasi.*' => 'nullable|integer|in:1,2,3,4',
            'jumlah_piutang.*' => 'nullable|numeric',  // Validasi untuk jumlah piutang
        ]);
        
        $updatedCount = 0;
        $noChanges = true;  // Menandakan apakah ada perubahan data
        $noInputChanges = true;  // Menandakan apakah ada perubahan input terbaru
        
        // Pastikan request->zonasi dan request->jumlah_piutang adalah array yang valid
        if (is_array($request->zonasi) && is_array($request->jumlah_piutang)) {
            foreach ($request->zonasi as $id => $zonasi) {
                $dataZonasi = DataZonasi::find($id);
                
                if ($dataZonasi) {
                    $jumlahPiutang = $request->input('jumlah_piutang.' . $id);
    
                    // Cek apakah jumlah piutang dan pembagian zonasi sudah diisi dengan benar
                    if (($zonasi && !$jumlahPiutang) || (!$zonasi && $jumlahPiutang)) {
                        // Jika salah satu diisi dan yang lain tidak diisi
                        return redirect()->route('admin.data_zonasi.data')->with('error', 'Gagal menyimpan. Pastikan jumlah piutang dan pembagian zonasi diisi bersamaan.');
                    }
    
                    // Cek apakah ada perubahan pada jumlah piutang dan pembagian zonasi
                    if (($zonasi && $dataZonasi->pembagian_zonasi != $zonasi) || ($jumlahPiutang && $dataZonasi->jumlah_piutang != $jumlahPiutang)) {
                        // Perbarui pembagian zonasi dan jumlah piutang jika ada perubahan
                        $dataZonasi->update([
                            'pembagian_zonasi' => $zonasi,
                            'jumlah_piutang' => $jumlahPiutang
                        ]);
                        $updatedCount++;
                        $noChanges = false;  // Ada perubahan data
                        $noInputChanges = false;  // Ada input terbaru
                    }
                }
            }
        }
    
        // Jika tidak ada input terbaru (tidak ada perubahan data)
        if ($noInputChanges) {
            return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada perubahan data untuk jumlah piutang dan pembagian zonasi.');
        }
    
        // Jika tidak ada perubahan data sama sekali
        if ($noChanges) {
            return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada data terbaru yang diinput.');
        }
    
        // Jika ada perubahan data
        return $updatedCount > 0
            ? redirect()->route('admin.data_zonasi.data')->with('success', 'Zonasi berhasil disimpan.')
            : redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada pembagian zonasi yang diperbarui.');
    }
    
    
    
// PESAN OTOMATIS
    // protected $fonnteService;

    // public function __construct(FonnteService $fonnteService)
    // {
    //     $this->fonnteService = $fonnteService; // Inisialisasi FonnteService
    // }

    // public function store(Request $request)
    // {
    //     // Validasi input dari form
    //     $validatedData = $request->validate([
    //         'zonasi.*' => 'nullable|integer|in:1,2,3,4',
    //         'jumlah_piutang.*' => 'nullable|numeric',  // Validasi untuk jumlah piutang
    //     ]);
        
    //     $updatedCount = 0;
    //     $noChanges = true;  // Menandakan apakah ada perubahan data
    //     $noInputChanges = true;  // Menandakan apakah ada perubahan input terbaru
    //     $failedToSendMessage = false; // Menandakan jika pesan gagal terkirim
    //     $errorMessages = [];

    //     // Pastikan request->zonasi dan request->jumlah_piutang adalah array yang valid
    //     if (is_array($request->zonasi) && is_array($request->jumlah_piutang)) {
    //         foreach ($request->zonasi as $id => $zonasi) {
    //             $dataZonasi = DataZonasi::find($id);
                
    //             if ($dataZonasi) {
    //                 $jumlahPiutang = $request->input('jumlah_piutang.' . $id);
    
    //                 // Cek apakah jumlah piutang dan pembagian zonasi sudah diisi dengan benar
    //                 if (($zonasi && !$jumlahPiutang) || (!$zonasi && $jumlahPiutang)) {
    //                     // Jika salah satu diisi dan yang lain tidak diisi
    //                     return redirect()->route('admin.data_zonasi.data')->with('error', 'Gagal menyimpan. Pastikan jumlah piutang dan pembagian zonasi diisi bersamaan.');
    //                 }
    
    //                 // Cek apakah ada perubahan pada jumlah piutang dan pembagian zonasi
    //                 if (($zonasi && $dataZonasi->pembagian_zonasi != $zonasi) || ($jumlahPiutang && $dataZonasi->jumlah_piutang != $jumlahPiutang)) {
    //                     // Kirim pesan WhatsApp terlebih dahulu sebelum menyimpan perubahan
    //                     if (!empty($dataZonasi->nomor_telepon)) {
    //                         $response = $this->fonnteService->sendMessage(
    //                             $dataZonasi->nomor_telepon,
    //                             "Zonasi dan jumlah piutang Anda telah diperbarui. Silakan cek kembali."
    //                         );
                            
    //                         // Cek jika pesan gagal terkirim
    //                         if ($response['status'] === 'error') {
    //                             $failedToSendMessage = true; // Tandai jika gagal mengirim pesan
    //                             $errorMessages[] = "Pesan gagal dikirim ke nomor {$dataZonasi->nomor_telepon}.";
    //                         }
    //                     }
                        
    //                     // Jika pengiriman pesan berhasil, simpan data Zonasi
    //                     if (!$failedToSendMessage) {
    //                         $dataZonasi->update([
    //                             'pembagian_zonasi' => $zonasi,
    //                             'jumlah_piutang' => $jumlahPiutang
    //                         ]);
    //                         $updatedCount++;
    //                         $noChanges = false;  // Ada perubahan data
    //                         $noInputChanges = false;  // Ada input terbaru
    //                     }
    //                 }
    //             }
    //         }
    //     }
    
    //     // Jika tidak ada input terbaru (tidak ada perubahan data)
    //     if ($noInputChanges) {
    //         return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada perubahan data untuk jumlah piutang dan pembagian zonasi.');
    //     }
    
    //     // Jika tidak ada perubahan data sama sekali
    //     if ($noChanges) {
    //         return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada data terbaru yang diinput.');
    //     }
    
    //     // Jika ada pesan yang gagal dikirim
    //     if ($failedToSendMessage) {
    //         return redirect()->route('admin.data_zonasi.data')->with('error', 'Zonasi tidak disimpan karena pesan WhatsApp gagal terkirim. ' . implode(' ', $errorMessages));
    //     }
    
    //     // Jika berhasil menyimpan data Zonasi dan pesan berhasil terkirim
    //     return $updatedCount > 0
    //         ? redirect()->route('admin.data_zonasi.data')->with('success', 'Zonasi berhasil disimpan dan pesan WhatsApp berhasil dikirim.')
    //         : redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada pembagian zonasi yang diperbarui.');
    // }
   


    // public function store(Request $request)
    // {
    //     // Validasi input dari form
    //     $validatedData = $request->validate([
    //         'zonasi.*' => 'nullable|integer|in:1,2,3,4',
    //         'jumlah_piutang.*' => 'nullable|numeric',
    //     ]);
        
    //     $updatedCount = 0;
    //     $noChanges = true;  // Menandakan apakah ada perubahan data
    //     $noInputChanges = true;  // Menandakan apakah ada perubahan input terbaru
    //     $failedToSendMessage = false; // Menandakan jika pesan gagal terkirim
    //     $errorMessages = [];
    
    //     // Pastikan request->zonasi dan request->jumlah_piutang adalah array yang valid
    //     if (is_array($request->zonasi) && is_array($request->jumlah_piutang)) {
    //         foreach ($request->zonasi as $id => $zonasi) {
    //             $dataZonasi = DataZonasi::find($id);
                
    //             if ($dataZonasi) {
    //                 $jumlahPiutang = $request->input('jumlah_piutang.' . $id);
    
    //                 // Cek apakah jumlah piutang dan pembagian zonasi sudah diisi dengan benar
    //                 if (($zonasi && !$jumlahPiutang) || (!$zonasi && $jumlahPiutang)) {
    //                     return redirect()->route('admin.data_zonasi.data')->with('error', 'Gagal menyimpan. Pastikan jumlah piutang dan pembagian zonasi diisi bersamaan.');
    //                 }
    
    //                 // Cek apakah ada perubahan pada jumlah piutang dan pembagian zonasi
    //                 if (($zonasi && $dataZonasi->pembagian_zonasi != $zonasi) || ($jumlahPiutang && $dataZonasi->jumlah_piutang != $jumlahPiutang)) {
    //                     // Kirim pesan WhatsApp terlebih dahulu sebelum menyimpan perubahan
    //                     if (!empty($dataZonasi->nomor_telepon)) {
    //                         $response = $this->fonnteService->sendMessage(
    //                             $dataZonasi->nomor_telepon,
    //                             "Zonasi dan jumlah piutang Anda telah diperbarui. Silakan cek kembali."
    //                         );
                            
    //                         // Cek jika pesan gagal terkirim
    //                         if ($response['status'] === 'error') {
    //                             $failedToSendMessage = true; // Tandai jika gagal mengirim pesan
    //                             $errorMessages[] = "Pesan gagal dikirim ke nomor {$dataZonasi->nomor_telepon}.";
                                
    //                             // Menampilkan detail pesan error menggunakan dd()
    //                             dd($response); // Tambahkan dd untuk menampilkan respons error
                                
    //                             continue;  // Jangan simpan perubahan jika pesan gagal
    //                         }
    //                     }
    
    //                     // Jika pengiriman pesan berhasil, simpan data Zonasi
    //                     if (!$failedToSendMessage) {
    //                         $dataZonasi->update([
    //                             'pembagian_zonasi' => $zonasi,
    //                             'jumlah_piutang' => $jumlahPiutang
    //                         ]);
    //                         $updatedCount++;
    //                         $noChanges = false;  // Ada perubahan data
    //                         $noInputChanges = false;  // Ada input terbaru
    //                     }
    //                 }
    //             }
    //         }
    //     }
    
    //     // Jika tidak ada input terbaru (tidak ada perubahan data)
    //     if ($noInputChanges) {
    //         return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada perubahan data untuk jumlah piutang dan pembagian zonasi.');
    //     }
    
    //     // Jika tidak ada perubahan data sama sekali
    //     if ($noChanges) {
    //         return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada data terbaru yang diinput.');
    //     }
    
    //     // Jika ada pesan yang gagal dikirim
    //     if ($failedToSendMessage) {
    //         return redirect()->route('admin.data_zonasi.data')->with('error', 'Zonasi tidak disimpan karena pesan WhatsApp gagal terkirim. ' . implode(' ', $errorMessages));
    //     }
    
    //     // Jika berhasil menyimpan data Zonasi dan pesan berhasil terkirim
    //     return $updatedCount > 0
    //         ? redirect()->route('admin.data_zonasi.data')->with('success', 'Zonasi berhasil disimpan dan pesan WhatsApp berhasil dikirim.')
    //         : redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada pembagian zonasi yang diperbarui.');
    // }
    


    public function sendMessage($to, $message)
{
    try {
        // Format nomor telepon, pastikan ada kode negara
        $target = '+' . preg_replace('/\D/', '', $to); // memastikan hanya nomor yang digunakan
    
        // Log untuk memeriksa format target
        \Log::info('Target Nomor Telepon:', ['target' => $target]);
    
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post($this->url, [
            'target' => $target,
            'message' => $message,
            'url' => 'https://md.fonnte.com/images/wa-logo.png', // opsional, jika ingin menambahkan URL
            'schedule' => 0,
            'typing' => false,
            'delay' => '2',
            'countryCode' => '62', // Pastikan ini sesuai dengan kode negara
        ]);
    
        // Log untuk respons API secara keseluruhan
        \Log::info('Fonnte API Response:', [
            'response_body' => $response->body(),
            'response_status' => $response->status()
        ]);
    
        if ($response->successful()) {
            $responseBody = $response->json();
            // Log response JSON jika API sukses
            \Log::info('Fonnte API JSON Response:', ['response' => $responseBody]);
    
            if (isset($responseBody['status']) && $responseBody['status'] === true) {
                return [
                    'status' => 'success',
                    'message' => $responseBody['message'] ?? 'Pesan berhasil dikirim.',
                ];
            }
    
            // Jika status response API adalah false
            return [
                'status' => 'error',
                'message' => $responseBody['message'] ?? 'Gagal mengirim pesan.',
            ];
        }
    
        // Jika respons API tidak berhasil (misalnya 4xx, 5xx)
        \Log::error('Fonnte API Error Response:', [
            'error_response' => $response->body(),
            'status_code' => $response->status()
        ]);
        return [
            'status' => 'error',
            'message' => 'Kesalahan saat menghubungi API Fonnte.',
            'response' => $response->body(),
            'status_code' => $response->status(),
        ];
    
    } catch (\Exception $e) {
        // Menangkap exception dan log error detailnya
        \Log::error('Fonnte API Exception:', [
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ]);
        return [
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat mengirim pesan.',
        ];
    }
}


    
    
public function showImportForm()
{
    return view('admin.data_zonasi.import'); // Buat view khusus untuk form input bulan
}

public function importFromWajibPajak(Request $request)
{
    // Validasi input bulan
    $request->validate([
        'bulan' => 'required|string|max:50',
    ]);

    $bulan = $request->input('bulan'); // Ambil nilai bulan dari input

    // Ambil data wajib pajak yang belum ada di DataZonasi dan statusnya belum lunas
    $wajibpajakData = DataWajibPajak::whereNotIn('npwpd', DataZonasi::pluck('npwpd'))
        ->where(function ($query) {
            $query->whereNull('status_lunas')
                  ->orWhere('status_lunas', 'Belum Lunas');
        })
        ->get();

    if ($wajibpajakData->isEmpty()) {
        return redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada data terbaru untuk diimpor.');
    }

    $newDataCount = 0;

    foreach ($wajibpajakData as $wajibpajak) {
        if (empty($wajibpajak->nomor_telepon)) {
            continue; // Lewati jika tidak ada nomor telepon
        }

        $existingZonasi = DataZonasi::where('npwpd', $wajibpajak->npwpd)->first();

        if ($existingZonasi) {
            if ($existingZonasi->jumlah_piutang != $wajibpajak->jumlah_piutang) {
                $existingZonasi->update(['jumlah_piutang' => $wajibpajak->jumlah_piutang]);
            }
            continue;
        }

        // Buat data baru dengan bulan
        DataZonasi::create([
            'nama_pajak' => $wajibpajak->nama_pajak,
            'alamat' => $wajibpajak->alamat,
            'npwpd' => $wajibpajak->npwpd,
            'nomor_telepon' => $wajibpajak->nomor_telepon,
            'jenis_pajak_id' => $wajibpajak->jenis_pajak_id,
            'kategori_pajak_id' => $wajibpajak->kategori_pajak_id,
            'jumlah_piutang' => $wajibpajak->jumlah_piutang ?? 0,
            'bulan' => $bulan,
        ]);

        $newDataCount++;
    }

    return $newDataCount > 0
        ? redirect()->route('admin.data_zonasi.data')->with('success', 'Data Zonasi berhasil diimpor.')
        : redirect()->route('admin.data_zonasi.data')->with('error', 'Tidak ada data baru yang memenuhi syarat.');
}
    
    
    
    

    
    
    public function filter(Request $request)
    {
        // Inisialisasi query untuk DataZonasi
        $query = DataZonasi::query();

        if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }

        // Pencarian berdasarkan kolom tertentu (misalnya nama_pajak dan alamat)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
            });
        }
    
        // Ambil data hasil filter
        $data = $query->get();
    
        // Menampilkan view dengan data yang sudah difilter
        return view('admin.data_zonasi.data', compact('data'));
    }
}