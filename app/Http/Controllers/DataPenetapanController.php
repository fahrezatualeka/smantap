<?php

namespace App\Http\Controllers;

use App\Models\DataPenetapan;
use App\Models\DataWajibPajak;
use App\Models\DataPiutang;
use App\Models\JenisPajak;
use App\Models\LaporanPelunasan;
use App\Models\KategoriPajak;
use App\Imports\DataPenetapanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;



use Illuminate\Http\Request;

class DataPenetapanController extends Controller
{
    public function index()
    {
        $dataPenetapan = DataPenetapan::all(); // Ambil semua data
        return view('admin.data_penetapan.data', compact('dataPenetapan'));
    }
    
    // public function index()
    // {
    //     $jenisPajak = JenisPajak::all();
    //     $kategoriPajak = KategoriPajak::all(); // Tambahkan ini
    //     return view('admin.data_penetapan.index', compact('jenisPajak', 'kategoriPajak'));
    // }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id', // Sesuaikan nama tabel
            'kategori_pajak_id' => 'nullable|integer|exists:kategoripajak,id', // Sesuaikan nama tabel
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
    
        $dataPenetapan = $query->get();
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
    
    public function updateStatus(Request $request, $penetapanId)
    {
        Log::info('Memulai updateStatus', ['penetapanId' => $penetapanId]);
    
        try {
            $penetapan = DataPenetapan::findOrFail($penetapanId);
            Log::info('Data Penetapan ditemukan', ['penetapan' => $penetapan]);
    
            $validated = $request->validate([
                'status' => 'required|in:belum_bayar,sudah_bayar',
                'jumlah_pembayaran' => 'required_if:status,sudah_bayar|numeric|min:1',
                'tanggal_pembayaran' => 'required_if:status,sudah_bayar|date',
                'bukti_pembayaran' => 'required_if:status,sudah_bayar|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'bukti_visit' => 'required_if:status,sudah_bayar|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);
    
            Log::info('Validasi berhasil', ['validated' => $validated]);
    
            Log::info('Bukti Pembayaran', ['exists' => $request->hasFile('bukti_pembayaran')]);
            Log::info('Bukti Visit', ['exists' => $request->hasFile('bukti_visit')]);
            

            // Update status penetapan
            $penetapan->status = $validated['status'];
    
            if ($validated['status'] === 'sudah_bayar') {
                if ($request->hasFile('bukti_pembayaran') && $request->hasFile('bukti_visit')) {
                    $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('uploads/pembayaran', 'public');
                    $buktiVisitPath = $request->file('bukti_visit')->store('uploads/visit', 'public');
                } else {
                    return redirect()->back()->with('error', 'Bukti pembayaran atau bukti visit tidak ditemukan.');
                }
                
                
    
                // Masukkan data ke tabel laporan pelunasan
                LaporanPelunasan::create([
                    'nama_pajak' => $penetapan->nama_pajak,
                    'alamat' => $penetapan->alamat,
                    'npwpd' => $penetapan->npwpd,
                    'jenis_pajak_id' => $penetapan->jenis_pajak_id,
                    'kategori_pajak_id' => $penetapan->kategori_pajak_id,
                    'jumlah_penagihan' => $penetapan->jumlah_penagihan,
                    'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
                    'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                    'buktipembayaran' => $buktiPembayaranPath,
                    'buktivisit' => $buktiVisitPath,
                ]);
                Log::info('Laporan pelunasan berhasil dibuat');
            }
    
            $penetapan->save();
            Log::info('Data Penetapan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error pada updateStatus: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    
        return redirect()->route('admin.data_penetapan.data')
            ->with('success', 'Status berhasil diperbarui.');
    }
    
public function destroy($id)
{
// Cari data yang akan dihapus
$dataPenetapan = DataPenetapan::findOrFail($id);

// Hapus data
$dataPenetapan->delete();

// Redirect dengan pesan sukses
return redirect()->route('admin.data_penetapan.data')->with('success', 'Data berhasil dihapus!');
}

}