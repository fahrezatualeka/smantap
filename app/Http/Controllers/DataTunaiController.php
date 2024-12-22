<?php

    namespace App\Http\Controllers;

    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    // use App\Models\DataPelunasan;
    use App\Models\DataTunai;
    use App\Models\LaporanTunai;
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


    
    class DataTunaiController extends Controller
    {
        public function index(Request $request)
        {
            $user = auth()->user(); // Ambil data user yang login
            $zonaUser = $user->zona; // Misal: zonasi dari atribut user
        
            // Filter data penagihan berdasarkan zonasi user dan metode pembayaran
            $dataTunai = DataTunai::where('zona', $zonaUser)
                ->where('metode_pembayaran', 'Tunai')
        ->orderBy('created_at', 'desc')

                ->get();
        
            return view('petugas_penagihan.data_tunai.data', compact('dataTunai'));
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
        
            $query = DataTunai::query();
        
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
            $dataTunai = $query->latest()->get();
        
            return view('petugas_penagihan.data_tunai.data', compact('dataTunai'));
        }

        public function showPaymentProof($id)
        {
            $dataTunai = DataTunai::find($id);
            if (!$dataTunai || !$dataTunai->buktipembayaran) {
                return abort(404, 'Bukti pembayaran tidak ditemukan.');
            }
                return response()->file(storage_path("app/public/{$dataTunai->buktipembayaran}"));
    
        }   
        public function showSspdProof($id)
        {
            $dataTunai = DataTunai::find($id);
            if (!$dataTunai || !$dataTunai->buktisspd) {
                return abort(404, 'Bukti sspd tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$dataTunai->buktisspd}"));
    
        }

public function uploadSspdInline(Request $request, $id)
{
    $request->validate([
        'photo_data' => 'nullable|string',
    ]);

    $dataTunai = DataTunai::find($id);
    if (!$dataTunai) {
        return redirect()->back()->with('error', 'Data tunai tidak ditemukan.');
    }

    $filePath = null;

    // Simpan foto dari webcam
    if ($request->photo_data) {
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request->photo_data));
        $fileName = 'uploads/sspd/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $imageData);
        $filePath = $fileName;
    }

    if ($filePath) {
        $dataTunai->update(['buktisspd' => $filePath]);

        // Update laporan transfer
        $laporanTunai = LaporanTunai::where('npwpd', $dataTunai->npwpd)
            ->where('periode', $dataTunai->periode)
            ->first();
        if ($laporanTunai) {
            $laporanTunai->update(['buktisspd' => $filePath]);
        }

        return redirect()->back()->with('success', 'Bukti SSPD berhasil diunggah.');
    }

    return redirect()->back()->with('error', 'Gagal mengunggah bukti SSPD.');
}

public function exportPdf(Request $request)
{
    $user = auth()->user(); // Ambil data user yang login
    $zonaUser = $user->zona; // Zonasi dari user yang login

    // Periksa apakah ada filter yang diterapkan pada data
    $query = DataTunai::where('zona', $zonaUser)->orderBy('created_at', 'desc');
    
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
    $dataTunai = $query->get();

    // Kirim data ke view khusus untuk PDF
    $pdf = Pdf::loadView('petugas_penagihan.data_tunai.pdf', compact('dataTunai'));

    // Unduh file PDF
    return $pdf->download('data_tunai_petugas.pdf');
}
    }