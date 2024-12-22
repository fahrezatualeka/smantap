<?php

    namespace App\Http\Controllers;

    use App\Models\DataPenagihan;
    use App\Models\JenisPajak;
    use App\Models\KategoriPajak;
    use App\Models\DataPelunasan;
    use App\Models\User;
    use App\Models\LaporanPelunasan;
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
    use Barryvdh\DomPDF\Facade\Pdf;

    
    class DataPelunasanController extends Controller
    {
        public function index(Request $request)
        {
            $user = auth()->user(); // Ambil data user yang login
            $zonaUser = $user->zona; // Misal: zonasi dari atribut user
        
            // Filter data penagihan berdasarkan zonasi user dan status
            $dataPelunasan = DataPelunasan::where('zona', $zonaUser)
                ->get();
        
            return view('petugas_penagihan.data_pelunasan.data', compact('dataPelunasan'));
        }

        public function showPaymentProof($id)
        {
            $dataPelunasan = DataPelunasan::find($id);
            if (!$dataPelunasan || !$dataPelunasan->buktipembayaran) {
                return abort(404, 'Bukti pembayaran tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$dataPelunasan->buktipembayaran}"));
    
        }   
        public function showVisitProof($id)
        {
            $dataPelunasan = dataPelunasan::find($id);
            if (!$dataPelunasan || !$dataPelunasan->buktivisit) {
                return abort(404, 'Bukti visit tidak ditemukan.');
            }
            return response()->file(storage_path("app/public/{$dataPelunasan->buktivisit}"));
    
        }
    }