<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPelunasan;
use App\Exports\LaporanPelunasanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;



class LaporanPelunasanController extends Controller
{
    public function index()
    {
        // $dataPelunasan = LaporanPelunasan::all();
        $dataPelunasan = LaporanPelunasan::with(['jenisPajak', 'kategoriPajak'])
        ->orderBy('created_at', 'desc')
        ->get();
    
        return view('admin.laporan_pelunasan.data', compact('dataPelunasan'));
    }

    public function filter(Request $request)
    {
        $query = LaporanPelunasan::query();
        
        if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pajak', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%');
            });
        }
        
        // $dataPelunasan = $query->get();
        $dataPelunasan = $query->orderBy('created_at', 'desc')->get();

        
        return view('admin.laporan_pelunasan.data', compact('dataPelunasan'));
    }

    public function showPaymentProof($id)
    {
        $laporanPelunasan = LaporanPelunasan::find($id);
        if (!$laporanPelunasan || !$laporanPelunasan->buktipembayaran) {
            return abort(404, 'Bukti pembayaran tidak ditemukan.');
        }
        return response()->file(storage_path("app/public/{$laporanPelunasan->buktipembayaran}"));

    }   
    public function showVisitProof($id)
    {
        $laporanPelunasan = LaporanPelunasan::find($id);
        if (!$laporanPelunasan || !$laporanPelunasan->buktivisit) {
            return abort(404, 'Bukti visit tidak ditemukan.');
        }
        return response()->file(storage_path("app/public/{$laporanPelunasan->buktivisit}"));

    }

    public function exportExcel()
    {
        return Excel::download(new LaporanPelunasanExport, 'laporan_pelunasan.xlsx');
    }
    
    public function exportPdf()
    {
        $laporanPelunasan = LaporanPelunasan::with(['jenisPajak', 'kategoriPajak'])
        ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at secara menurun
        ->get();;
    
        $pdf = Pdf::loadView('admin.laporan_pelunasan.pdf', compact('laporanPelunasan'));
    
        // Unduh file PDF
        return $pdf->download('laporan_pelunasan.pdf');
    }
}