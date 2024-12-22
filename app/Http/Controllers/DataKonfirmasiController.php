<?php

    namespace App\Http\Controllers;

    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    // use App\Models\DataPelunasan;
    use App\Models\DataKonfirmasi;
    use App\Models\User;
    use App\Models\LaporanPelunasan;
    use App\Models\LaporanPiutang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\QueryException;
    use App\Exports\DataPiutangExport;
    use Barryvdh\DomPDF\Facade\Pdf;
    use Carbon\Carbon;


    
    class DataKonfirmasiController extends Controller
    {
        public function index(Request $request)
        {
            $user = auth()->user(); // Ambil data user yang login
            $zonaUser = $user->zona; // Misal: zonasi dari atribut user
        
            // Filter data penagihan berdasarkan zonasi user dan metode pembayaran
            $dataKonfirmasi = DataKonfirmasi::where('zona', $zonaUser)
                ->where('metode_pembayaran', 'Konfirmasi')
        ->orderBy('created_at', 'desc')

                ->get();
        
            return view('petugas_penagihan.data_konfirmasi.data', compact('dataKonfirmasi'));
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
        
            $query = DataKonfirmasi::query();
        
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
            $dataKonfirmasi = $query->latest()->get();
        
            return view('petugas_penagihan.data_konfirmasi.data', compact('dataKonfirmasi'));
        }
        


        public function showVisitProof($id)
        {
            $dataKonfirmasi = DataKonfirmasi::find($id);
            if (!$dataKonfirmasi || !$dataKonfirmasi->buktivisit) {
                return abort(404, 'Bukti visit tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$dataKonfirmasi->buktivisit}"));
    
        }


        public function exportPdf(Request $request)
        {
            $user = auth()->user(); // Ambil data user yang login
            $zonaUser = $user->zona; // Zonasi dari user yang login
        
            // Periksa apakah ada filter yang diterapkan pada data
            $query = DataKonfirmasi::where('zona', $zonaUser)->orderBy('created_at', 'desc');
            
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
            $dataKonfirmasi = $query->get();
        
            // Kirim data ke view khusus untuk PDF
            $pdf = Pdf::loadView('petugas_penagihan.data_konfirmasi.pdf', compact('dataKonfirmasi'));
        
            // Unduh file PDF
            return $pdf->download('data_konfirmasi_petugas.pdf');
        }
    
    }