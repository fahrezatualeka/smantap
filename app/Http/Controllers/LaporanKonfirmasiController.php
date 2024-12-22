<?php

    namespace App\Http\Controllers;

    use App\Models\DataPenagihan;
    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    // use App\Models\DataPelunasan;
    use App\Models\LaporanKonfirmasi;
    use App\Models\User;
    // use App\Models\LaporanPelunasan;
    use App\Models\LaporanPiutang;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;
    use App\Exports\LaporanKonfirmasiExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\QueryException;
    use App\Exports\DataPiutangExport;
    use Barryvdh\DomPDF\Facade\Pdf;
    use Carbon\Carbon;


    
    class LaporanKonfirmasiController extends Controller
    {
        public function index(Request $request)
        {
            // $laporanKonfirmasi = LaporanKonfirmasi::all()
        $laporanKonfirmasi = LaporanKonfirmasi::with(['jenisPajak'])
                ->where('metode_pembayaran', 'Konfirmasi')
        ->orderBy('created_at', 'desc')

                ->get();
        
            return view('admin.laporan_konfirmasi.data', compact('laporanKonfirmasi'));
        }
        


        public function showVisitProof($id)
        {
            $laporanKonfirmasi = LaporanKonfirmasi::find($id);
            if (!$laporanKonfirmasi || !$laporanKonfirmasi->buktivisit) {
                return abort(404, 'Bukti visit tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$laporanKonfirmasi->buktivisit}"));
    
        }

        public function filter(Request $request)
        {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
                'zona' => 'nullable|integer',
                'bulan' => 'nullable|string|max:15',
            ]);
        
            $query = LaporanKonfirmasi::query();
        
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
        
            $laporanKonfirmasi = $query->latest()->get();
        
            return view('admin.laporan_konfirmasi.data', compact('laporanKonfirmasi'));
        }

        public function exportExcel()
        {
            return Excel::download(new LaporanKonfirmasiExport, 'laporan_konfirmasi.xlsx');
        }
        
        public function exportPdf(Request $request)
{
    // Mulai query untuk LaporanKonfirmasi
    $query = LaporanKonfirmasi::query();

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
    $laporanKonfirmasi = $query->latest()->get();

    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('admin.laporan_konfirmasi.pdf', compact('laporanKonfirmasi'));

    // Unduh file PDF
    return $pdf->download('laporan_konfirmasi.pdf');
}
    
    }