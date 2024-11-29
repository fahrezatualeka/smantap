<?php

namespace App\Http\Controllers;

use App\Models\DataPenetapan;
use App\Models\DataWajibPajak;
use App\Models\DataPenagihan;
use App\Models\DataPiutang;
use App\Models\JenisPajak;
use App\Models\LaporanPelunasan;
use App\Models\LaporanPiutang;
use App\Models\KategoriPajak;
use App\Imports\DataPenetapanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\DataPenetapanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;



use Illuminate\Http\Request;

class DataPenetapanController extends Controller
{

//     public function index()
// {
//     // Ambil data dengan sorting
//     $dataPenetapan = DataPenetapan::orderByRaw("
//         CASE 
//             WHEN status = 'Belum Bayar' THEN 0
//             WHEN status = 'Sudah Bayar' THEN 1
//         END ASC
//     ")->latest() // Data terbaru berada di atas
//       ->get();

//     return view('admin.data_penetapan.data', compact('dataPenetapan'));
// }

public function index()
{
    // Ambil data dengan sorting dan relasi
    $dataPenetapan = DataPenetapan::with(['jenisPajak', 'kategoriPajak'])
        ->orderByRaw("
            CASE 
                WHEN status = 'Belum Bayar' THEN 0
                WHEN status = 'Sudah Bayar' THEN 1
            END ASC
        ")->latest()
        ->get();

    return view('admin.data_penetapan.data', compact('dataPenetapan'));
}


    public function filter(Request $request)
    {
        $validated = $request->validate([
            'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
            'kategori_pajak_id' => 'nullable|integer|exists:kategoripajak,id',
            'search' => 'nullable|string|max:255',
        ]);
    
        $query = DataPenetapan::query();
    
        if ($request->filled('jenis_pajak_id')) {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
    
        if ($request->filled('kategori_pajak_id')) {
            $query->where('kategori_pajak_id', $request->kategori_pajak_id);
        }
    
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pajak', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $request->search . '%');
            });
        }
    
        $dataPenetapan = $query->orderByRaw("
            CASE 
                WHEN status = 'Belum Bayar' THEN 0
                WHEN status = 'Sudah Bayar' THEN 1
            END ASC
        ")->latest()->get();
    
        return view('admin.data_penetapan.data', compact('dataPenetapan'));
    }
    

    public function create()
    {
        // Ambil data NPWPD yang sudah ada di DataPenetapan
        $existingNpwpds = DataPenetapan::pluck('npwpd')->toArray();
        
        // Ambil data NPWPD dan nama_pajak yang belum ada di DataPenetapan
        $dataWajibPajak = DataWajibPajak::whereNotIn('npwpd', $existingNpwpds)
                                         ->select('npwpd', 'nama_pajak')
                                         ->get();
        
        return view('admin.data_penetapan.add', compact('dataWajibPajak'));
    }
    
    

    public function store(Request $request)
    {
        $request->validate([
            'npwpd' => 'required|exists:datawajibpajak,npwpd',
            'jumlah_penagihan' => 'required|numeric',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
        ]);
    
        // Cari data wajib pajak berdasarkan npwpd
        $wajibPajak = DataWajibPajak::where('npwpd', $request->npwpd)->first();
    
        // Ambil jenis dan kategori pajak dari data wajib pajak (jika sudah ada relasi atau field terkait)
        $jenisPajakId = $wajibPajak->jenis_pajak_id ?? null; // Sesuaikan nama kolomnya
        $kategoriPajakId = $wajibPajak->kategori_pajak_id ?? null; // Sesuaikan nama kolomnya
    
        // Simpan data penetapan
        DataPenetapan::create([
            'npwpd' => $wajibPajak->npwpd,
            'nama_pajak' => $wajibPajak->nama_pajak,
            'alamat' => $wajibPajak->alamat,
            'jumlah_penagihan' => $request->jumlah_penagihan,
            'jenis_pajak_id' => $jenisPajakId,
            'kategori_pajak_id' => $kategoriPajakId,
            'nomor_telepon' => $wajibPajak->nomor_telepon,
            'pembagian_zonasi' => $wajibPajak->pembagian_zonasi,
            'periode' => $request->bulan . ' ' . $request->tahun,
        ]);
    
        return redirect()->route('admin.data_penetapan.data')->with('success', 'Data berhasil ditambahkan!');
    }
    
    public function edit($id)
    {
        // Ambil data penetapan berdasarkan id
        $dataPenetapan = DataPenetapan::findOrFail($id);
        
        // Ambil periode (jika ada) untuk menentukan bulan dan tahun default
        $periode = $dataPenetapan->periode ?? ''; // Contoh format: "2025-12"
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
        return view('admin.data_penetapan.edit', compact('dataPenetapan', 'bulan', 'tahun', 'months'));
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
        $dataPenetapan = DataPenetapan::findOrFail($id);
        
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
        $dataPenetapan->update([
            'jumlah_penagihan' => $request->jumlah_penagihan,
            'periode' => "$bulanNama $request->tahun", // Formatkan periode sebagai "Bulan Tahun"
        ]);
    
        return redirect()->route('admin.data_penetapan.data')->with('success', 'Data berhasil diperbarui!');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:2048',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
        ]);
        
        // Cek apakah file ada
        if ($request->hasFile('file')) {
            Log::info('File diterima: ', ['file' => $request->file('file')->getClientOriginalName()]);
        } else {
            Log::error('File tidak ditemukan');
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
    
        try {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
    
            // Pemetaan bulan dalam bahasa Inggris ke bahasa Indonesia
            $monthMapping = [
                'january' => '01', 'february' => '02', 'march' => '03',
                'april' => '04', 'may' => '05', 'june' => '06',
                'july' => '07', 'august' => '08', 'september' => '09',
                'october' => '10', 'november' => '11', 'december' => '12'
            ];
    
            // Convert bulan ke format lowercase dan periksa apakah valid
            $bulan = strtolower($bulan);  // Pastikan input menjadi huruf kecil
            if (!isset($monthMapping[$bulan])) {
                throw new \Exception("Bulan tidak valid: " . ucfirst($bulan));
            }
    
            // Ambil bulan dalam angka
            $bulanAngka = $monthMapping[$bulan];
            $periode = "{$tahun}-{$bulanAngka}"; // Formatkan periode menjadi "YYYY-MM"
    
            // Proses impor data
            Excel::import(new DataPenetapanImport($periode), $request->file('file'));
            
            Log::info('Data berhasil diimpor ke tabel Data Penetapan', ['periode' => $periode]);
    
            return redirect()->back()->with('success', 'Data berhasil diimpor ke tabel Data Penetapan!');
        } catch (\Exception $e) {
            Log::error('Gagal mengimpor data:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, $id)
    {
        try {
            // Ambil data dari DataPenetapan berdasarkan ID
            $dataPenetapan = DataPenetapan::findOrFail($id);
    
            // Validasi data yang diterima dari request
            $validated = $request->validate([
                'status' => 'required|string',
                // 'jumlah_pembayaran' => 'required|numeric|min:0',
                'tanggal_pembayaran' => 'required|date',
                'bukti_pembayaran' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'bukti_visit' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            ]);
    
            // Update status di DataPenetapan
            $dataPenetapan->update(['status' => $validated['status']]);

    
            if ($validated['status'] === 'Sudah Bayar') {
                // Upload file bukti pembayaran
                $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('uploads/pembayaran', 'public');
            
                $buktiVisitPath = $request->hasFile('bukti_visit') 
                    ? $request->file('bukti_visit')->store('uploads/visit', 'public') 
                    : null;
            
                LaporanPelunasan::create([
                    'nama_pajak' => $dataPenetapan->nama_pajak,
                    'alamat' => $dataPenetapan->alamat,
                    'npwpd' => $dataPenetapan->npwpd,
                    'jenis_pajak_id' => $dataPenetapan->jenis_pajak_id,
                    'kategori_pajak_id' => $dataPenetapan->kategori_pajak_id,
                    'nomor_telepon' => $dataPenetapan->nomor_telepon,
                    'pembagian_zonasi' => $dataPenetapan->pembagian_zonasi,
                    'jumlah_penagihan' => $dataPenetapan->jumlah_penagihan,
                    // 'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
                    'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                    'buktipembayaran' => $buktiPembayaranPath,
                    'buktivisit' => $buktiVisitPath,
                    'tempat_pembayaran' => 'Admin',
                ]);
            
                Log::info('Data berhasil disalin ke LaporanPelunasan.', ['npwpd' => $dataPenetapan->npwpd]);
            }
    
            return redirect()->route('admin.data_penetapan.data')
                ->with('success', 'Data berhasil diperbarui dan diproses.');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat memproses data.', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }
    
    
    
    
    public function destroy($id)
    {
        // Cari data penetapan berdasarkan ID
        $dataPenetapan = DataPenetapan::findOrFail($id);
    
        // Periksa status penetapan
        if ($dataPenetapan->status === 'Belum Bayar') {
            // Jika status "belum bayar", hapus data yang terkait di tabel DataPiutang dan LaporanPiutang
            DataPiutang::where('npwpd', $dataPenetapan->npwpd)->delete();
            LaporanPiutang::where('npwpd', $dataPenetapan->npwpd)->delete();
        } elseif ($dataPenetapan->status === 'Sudah Bayar') {
            // Jika status "sudah bayar", hapus data yang terkait di tabel LaporanPelunasan
            LaporanPelunasan::where('npwpd', $dataPenetapan->npwpd)->delete();
        }
    
        // Hapus data penetapan
        $dataPenetapan->delete();
    
        // Redirect dengan pesan sukses
        return redirect()->route('admin.data_penetapan.data')->with('success', 'Data berhasil dihapus!');
    }

    public function exportExcel()
{
    return Excel::download(new DataPenetapanExport, 'data_penetapan.xlsx');
}

public function exportPdf()
{
    // Ambil data yang akan ditampilkan di PDF
    $dataPenetapan = DataPenetapan::with(['jenisPajak', 'kategoriPajak'])
        ->orderByRaw("
            CASE 
                WHEN status = 'Belum Bayar' THEN 0
                WHEN status = 'Sudah Bayar' THEN 1
            END ASC
        ")->latest()->get();

    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('admin.data_penetapan.pdf', compact('dataPenetapan'));

    // Unduh file PDF
    return $pdf->download('data_penetapan.pdf');
}
    

}