<?php

    namespace App\Http\Controllers;

    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    // use App\Models\DataPelunasan;
    use App\Models\LaporanPenutupan;
    use App\Models\User;
    // use App\Models\LaporanPelunasan;
    use App\Models\LaporanPiutang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use App\Exports\LaporanPenutupanExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\QueryException;
    use Barryvdh\DomPDF\Facade\Pdf;
    use Carbon\Carbon;


    
    class LaporanPenutupanController extends Controller
    {
        public function index(Request $request)
        {
            // $laporanTransfer = LaporanPenutupan::all()
        $laporanPenutupan = LaporanPenutupan::with(['jenisPajak'])
                ->where('metode_pembayaran', 'Penutupan')
        ->orderBy('created_at', 'desc')

                ->get();
        
            return view('admin.laporan_penutupan.data', compact('laporanPenutupan'));
        }
        


        public function showPenutupanProof($id)
        {
            $laporanPenutupan = LaporanPenutupan::find($id);
            if (!$laporanPenutupan || !$laporanPenutupan->buktipenutupan) {
                return abort(404, 'Bukti penutupan tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$laporanPenutupan->buktipenutupan}"));
    
        }

        public function filter(Request $request)
        {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
                'zona' => 'nullable|integer',
                'bulan' => 'nullable|string|max:15',
            ]);
        
            $query = LaporanPenutupan::query();
        
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
        
            $laporanPenutupan = $query->latest()->get();
        
            return view('admin.laporan_penutupan.data', compact('laporanPenutupan'));
        }

        public function exportExcel()
        {
            return Excel::download(new LaporanPenutupanExport, 'laporan_penutupan.xlsx');
        }
        
        public function exportPdf(Request $request)
        {
            // Mulai query untuk LaporanPenutupan
            $query = LaporanPenutupan::query();
        
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
            $laporanPenutupan = $query->latest()->get();
        
            // Kirim data ke view khusus untuk PDF
            $pdf = Pdf::loadView('admin.laporan_penutupan.pdf', compact('laporanPenutupan'));
        
            // Unduh file PDF
            return $pdf->download('laporan_penutupan.pdf');
        }
    }