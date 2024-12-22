<?php

namespace App\Http\Controllers;

use App\Models\DataWajibPajak;
use App\Models\DataPenagihan;
use App\Models\DataPiutang;
use App\Models\JenisPajak;
use App\Models\LaporanPelunasan;
// use App\Models\LaporanPiutang;
use App\Models\KategoriPajak;
use App\Imports\DataPiutangImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\DataPiutangExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\FonnteService;
use GuzzleHttp\Client;



use Illuminate\Http\Request;

class DataPiutangController extends Controller
{

public function index()
{
    $dataPiutang = DataPiutang::with(['jenisPajak'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.data_piutang.data', compact('dataPiutang'));
}


public function filter(Request $request)
{
    $validated = $request->validate([
        'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
        'search' => 'nullable|string|max:255',
        'bulan' => 'nullable|string|max:15', // Validasi bulan
        'zona' => 'nullable|integer', // Tidak validasi relasi karena bukan foreign key

    ]);

    $query = DataPiutang::query();

    if ($request->filled('jenis_pajak_id')) {
        $query->where('jenis_pajak_id', $request->jenis_pajak_id);
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
              ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
        });
    }

    if ($request->filled('bulan')) {
        // Mapping nama bulan
        $bulanMapping = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
        ];

        $namaBulan = $bulanMapping[$request->bulan] ?? null;

        if ($namaBulan) {
            // Filter berdasarkan nama bulan di `periode`
            $query->where('periode', 'LIKE', $namaBulan . '%');
        }
    }
    
    if ($request->filled('zona')) {
        $query->where('zona', $request->zona);
    }

    $dataPiutang = $query->latest()->get();

    return view('admin.data_piutang.data', compact('dataPiutang'));
}
    

    public function create()
    {
        // Ambil data NPWPD yang sudah ada di DataPiutang
        $existingNpwpds = DataPiutang::pluck('npwpd')->toArray();
        
        // Ambil data NPWPD dan nama_pajak yang belum ada di DataPiutang
        $dataWajibPajak = DataWajibPajak::whereNotIn('npwpd', $existingNpwpds)
                                         ->select('npwpd', 'nama_pajak')
                                         ->get();
        
        return view('admin.data_piutang.add', compact('dataWajibPajak'));
    }
    
    

    public function store(Request $request)
    {
        $request->validate([
            'npwpd' => 'required|exists:datawajibpajak,npwpd',
            // 'tagihan' => 'required|numeric',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
        ]);
    
        // Cari data wajib pajak berdasarkan npwpd
        $wajibPajak = DataWajibPajak::where('npwpd', $request->npwpd)->first();
    
        // Ambil jenis dan kategori pajak dari data wajib pajak
        $jenisPajakId = $wajibPajak->jenis_pajak_id ?? null;
        // $kategoriPajakId = $wajibPajak->kategori_pajak_id ?? null;
    
        // Simpan data penetapan
        $dataPiutang = DataPiutang::create([
            'npwpd' => $wajibPajak->npwpd,
            'nama_pajak' => $wajibPajak->nama_pajak,
            'alamat' => $wajibPajak->alamat,
            // 'tagihan' => $request->tagihan,
            'jenis_pajak_id' => $jenisPajakId,
            // 'kategori_pajak_id' => $kategoriPajakId,
            'telepon' => $wajibPajak->telepon,
            'zona' => $wajibPajak->zona,
            'periode' => $request->bulan . ' ' . $request->tahun,
            'status' => 'Belum Bayar', // Menambahkan status default
        ]);
    
        // Jika statusnya 'Belum Bayar', tambahkan data ke LaporanPiutang dan DataPenagihan
        if ($dataPiutang->status === 'Belum Bayar') {
            // Masukkan data ke LaporanPiutang
            DataPiutang::create([
                'nama_pajak' => $dataPiutang->nama_pajak,
                'alamat' => $dataPiutang->alamat,
                'npwpd' => $dataPiutang->npwpd,
                'jenis_pajak_id' => $dataPiutang->jenis_pajak_id,
                // 'kategori_pajak_id' => $dataPiutang->kategori_pajak_id,
                'telepon' => $dataPiutang->telepon,
                'zona' => $dataPiutang->zona,
                // 'tagihan' => $dataPiutang->tagihan,
                'periode' => $dataPiutang->periode,
            ]);
    
            // Masukkan data ke DataPenagihan
            $zonaArray = explode(',', $dataPiutang->zona);
            foreach ($zonaArray as $zona) {
                DataPenagihan::create([
                    'nama_pajak' => $dataPiutang->nama_pajak,
                    'alamat' => $dataPiutang->alamat,
                    'npwpd' => $dataPiutang->npwpd,
                    'jenis_pajak_id' => $dataPiutang->jenis_pajak_id,
                    // 'kategori_pajak_id' => $dataPiutang->kategori_pajak_id,
                    'telepon' => $dataPiutang->telepon,
                    'zona' => trim($zona),
                    // 'jumlah_penagihan' => $dataPiutang->jumlah_penagihan,
                    'periode' => $dataPiutang->periode,
                    'status' => 'Belum Bayar',
                ]);
            }
        }
    
        return redirect()->route('admin.data_piutang.data')->with('success', 'Data berhasil ditambahkan!');
    }
    
    public function edit($id)
    {
        // Ambil data penetapan berdasarkan id
        $dataPiutang = DataPiutang::findOrFail($id);
        
        // Ambil periode (jika ada) untuk menentukan bulan dan tahun default
        $periode = $dataPiutang->periode ?? ''; // Contoh format: "2025-12"
        [$tahun, $bulan] = array_pad(explode('-', $periode), 2, ''); // Fallback jika explode gagal
        
        // Definisikan daftar bulan
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        // Kirimkan data ke view
        return view('admin.data_piutang.edit', compact('dataPiutang', 'bulan', 'tahun', 'months'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'jumlah_penagihan' => 'required|numeric',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);
    
        // Cari data penetapan berdasarkan id
        $dataPiutang = DataPiutang::findOrFail($id);
        
        // Definisikan daftar bulan
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        
        // Ubah bulan angka menjadi nama bulan
        $bulanNama = $months[$request->bulan];
        
        // Update kolom yang diperlukan
        $dataPiutang->update([
            'tagihan' => $request->tagihan,
            'periode' => "$bulanNama $request->tahun", // Formatkan periode sebagai "Bulan Tahun"
        ]);
    
        return redirect()->route('admin.data_piutang.data')->with('success', 'Data berhasil diperbarui!');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:2048',
            'bulan' => 'required|string',
        ]);
    
        $monthMapping = [
            'january' => 'Januari', 'february' => 'Februari', 'march' => 'Maret',
            'april' => 'April', 'may' => 'Mei', 'june' => 'Juni',
            'july' => 'Juli', 'august' => 'Agustus', 'september' => 'September',
            'october' => 'Oktober', 'november' => 'November', 'december' => 'Desember',
        ];
    
        $bulan = strtolower($request->bulan);
        $bulanNama = $monthMapping[$bulan] ?? null;
    
        if (!$bulanNama) {
            return redirect()->back()->with('error', 'Bulan tidak valid.');
        }
    
        $tahun = date('Y');
        $periode = "{$bulanNama} {$tahun}";
    
        try {
            // Impor data ke DataPiutang
            Excel::import(new DataPiutangImport($periode), $request->file('file'));
    
            // Ambil data yang diimpor berdasarkan periode
            $dataPiutang = DataPiutang::where('periode', $periode)->get();
    
            foreach ($dataPiutang as $data) {
                // Cek apakah NPWPD dan periode sudah ada di DataPenagihan
                $existingPenagihan = DataPenagihan::where('npwpd', $data->npwpd)
                    ->where('periode', $periode)
                    ->exists();
    
                if (!$existingPenagihan) {
                    // Tambahkan data ke tabel DataPenagihan
                    DataPenagihan::create([
                        'nama_pajak' => $data->nama_pajak,
                        'alamat' => $data->alamat,
                        'npwpd' => $data->npwpd,
                        'jenis_pajak_id' => $data->jenis_pajak_id,
                        'telepon' => $data->telepon,
                        'zona' => $data->zona,
                        'periode' => $periode,
                        'status' => 'Belum Bayar',
                    ]);

                    // Kirim pesan WhatsApp jika ada telepon
                    // $telepon = $data->telepon;
                    // if ($telepon) {
                    //     $formattedPhone = preg_replace('/\D/', '', $telepon);
                    //     $formattedPhone = '62' . substr($formattedPhone, 1);

                    //     $message = "Yang Terhormat Wajib Pajak {$data->nama_pajak}, dengan npwpd {$data->npwpd}.\n\nSistem kami mendeteksi unit usaha anda memiliki piutang pajak periode {$data->periode}.\n\nKami berharap anda dapat segera menyelesaikan piutang pajak tersebut bulan ini agar tidak dikenakan denda pajak progresif.\n\n Terimakasih.";
                    //     $this->sendMessage($formattedPhone, $message);
                    // }
                }
            }
    
            return redirect()->back()->with('success', 'Data berhasil diimpor!');
        } catch (\Exception $e) {
            Log::error('Error saat mengimpor data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data.');
        }
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
                        'contents' => '62',  // Kode negara Indonesia
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

    
    
    // public function updateStatus(Request $request, $id)
    // {
    //     try {
    //         $dataPiutang = DataPiutang::findOrFail($id);

    //         $validated = $request->validate([
    //             'status' => 'required|string',
    //             'tanggal_pembayaran' => 'required|date',
    //             'bukti_pembayaran' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
    //             'bukti_visit' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
    //         ]);
    
    //         // Update status
    //         $dataPiutang->update(['status' => $validated['status']]);
    
    //         if ($validated['status'] === 'Sudah Bayar') {
    //             // Hapus data dari LaporanPiutang dan DataPenagihan
    //             DataPiutang::where('npwpd', $dataPiutang->npwpd)->delete();
    //             DataPenagihan::where('npwpd', $dataPiutang->npwpd)->delete();
    
    //             // Simpan bukti pembayaran
    //             $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('uploads/pembayaran', 'public');
    //             $buktiVisitPath = $request->file('bukti_visit') ? $request->file('bukti_visit')->store('uploads/visit', 'public') : null;
    
    //             // Tambahkan ke LaporanPelunasan
    //             LaporanPelunasan::create([
    //                 'nama_pajak' => $dataPiutang->nama_pajak,
    //                 'alamat' => $dataPiutang->alamat,
    //                 'npwpd' => $dataPiutang->npwpd,
    //                 'jenis_pajak_id' => $dataPiutang->jenis_pajak_id,
    //                 // 'kategori_pajak_id' => $dataPiutang->kategori_pajak_id,
    //                 'telepon' => $dataPiutang->telepon,
    //                 'zona' => $dataPiutang->zona,
    //                 'tagihan' => $dataPiutang->tagihan,
    //                 'periode' => $dataPiutang->periode,
    //                 'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
    //                 'buktipembayaran' => $buktiPembayaranPath,
    //                 'buktivisit' => $buktiVisitPath,
    //                 'tempat_pembayaran' => 'Admin',
    //             ]);
    //         }
    
    //         return redirect()->route('admin.data_piutang.data')
    //             ->with('success', 'Status berhasil diperbarui.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
    //     }
    // }

    // public function updateStatus(Request $request, $id)
    // {
    //     try {
    //         // Ambil data dari DataPiutang berdasarkan ID
    //         $dataPiutang = DataPiutang::findOrFail($id);
    
    //         // Validasi data yang diterima dari request
    //         $validated = $request->validate([
    //             'status' => 'required|string',
    //             // 'jumlah_pembayaran' => 'required|numeric|min:0',
    //             'tanggal_pembayaran' => 'required|date',
    //             'bukti_pembayaran' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
    //             'bukti_visit' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
    //         ]);
    
    //         // Update status di DataPiutang
    //         $dataPiutang->update(['status' => $validated['status']]);

    
    //         if ($validated['status'] === 'Sudah Bayar') {
    //             // Upload file bukti pembayaran
    //             $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('uploads/pembayaran', 'public');
            
    //             $buktiVisitPath = $request->hasFile('bukti_visit') 
    //                 ? $request->file('bukti_visit')->store('uploads/visit', 'public') 
    //                 : null;
            
    //             LaporanPelunasan::create([
    //                 'nama_pajak' => $dataPiutang->nama_pajak,
    //                 'alamat' => $dataPiutang->alamat,
    //                 'npwpd' => $dataPiutang->npwpd,
    //                 'jenis_pajak_id' => $dataPiutang->jenis_pajak_id,
    //                 'kategori_pajak_id' => $dataPiutang->kategori_pajak_id,
    //                 'nomor_telepon' => $dataPiutang->nomor_telepon,
    //                 'pembagian_zonasi' => $dataPiutang->pembagian_zonasi,
    //                 'jumlah_penagihan' => $dataPiutang->jumlah_penagihan,
    //                 'periode' => $dataPiutang->periode,
    //                 // 'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
    //                 'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
    //                 'buktipembayaran' => $buktiPembayaranPath,
    //                 'buktivisit' => $buktiVisitPath,
    //                 'tempat_pembayaran' => 'Admin',
    //             ]);
            
    //             Log::info('Data berhasil disalin ke LaporanPelunasan.', ['npwpd' => $dataPiutang->npwpd]);
    //         }
    
    //         return redirect()->route('admin.data_piutang.data')
    //             ->with('success', 'Data berhasil diperbarui dan diproses.');
    //     } catch (\Exception $e) {
    //         Log::error('Terjadi kesalahan saat memproses data.', ['error' => $e->getMessage()]);
    //         return redirect()->back()->with('error', 'Gagal memproses data: ' . $e->getMessage());
    //     }
    // }
    
    
    
    
    // public function destroy($id)
    // {
    //     // Cari data penetapan berdasarkan ID
    //     $dataPiutang = DataPiutang::findOrFail($id);
    
    //     // Periksa status penetapan
    //     if ($dataPiutang->status === 'Belum Bayar') {
    //         // Jika status "belum bayar", hapus data yang terkait di tabel DataPiutang dan LaporanPiutang
    //         DataPiutang::where('npwpd', $dataPiutang->npwpd)->delete();
    //         DataPiutang::where('npwpd', $dataPiutang->npwpd)->delete();
    //     } elseif ($dataPiutang->status === 'Sudah Bayar') {
    //         // Jika status "sudah bayar", hapus data yang terkait di tabel LaporanPelunasan
    //         LaporanPelunasan::where('npwpd', $dataPiutang->npwpd)->delete();
    //     }
    
    //     // Hapus data penetapan
    //     $dataPiutang->delete();
    
    //     // Redirect dengan pesan sukses
    //     return redirect()->route('admin.data_piutang.data')->with('success', 'Data berhasil dihapus!');
    // }

    public function exportExcel()
{
    return Excel::download(new DataPiutangExport, 'data_piutang.xlsx');
}

public function exportPdf(Request $request)
{
    // Membuat query untuk DataPiutang
    $query = DataPiutang::query();

    // Filter berdasarkan jenis pajak
    if ($request->filled('jenis_pajak_id')) {
        $query->where('jenis_pajak_id', $request->jenis_pajak_id);
    }

    // Filter berdasarkan pencarian
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
              ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
        });
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

    // Filter berdasarkan zona
    if ($request->filled('zona')) {
        $query->where('zona', $request->zona);
    }

    // Ambil data yang telah difilter
    $dataPiutang = $query->latest()->get();

    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('admin.data_piutang.pdf', compact('dataPiutang'));

    // Unduh file PDF
    return $pdf->download('data_piutang.pdf');
}
    

}