<?php

namespace App\Http\Controllers;

use App\Models\LaporanPiutang;
use App\Models\DataPenetapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exports\LaporanPiutangExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;


class LaporanPiutangController extends Controller
{
    public function index()
    {
        $laporanPiutang = LaporanPiutang::with(['jenisPajak', 'kategoriPajak'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.laporan_piutang.data', compact('laporanPiutang'));
    }
    
    
    public function filter(Request $request)
    {
        $query = LaporanPiutang::query();
        
        // Filter berdasarkan jenis pajak
        if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
        
        // Pencarian berdasarkan nama atau alamat
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pajak', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Mengambil data berdasarkan query yang sudah difilter
        $laporanPiutang = $query->get();
        
        // Mengirimkan data ke view
        return view('admin.laporan_piutang.data', compact('laporanPiutang'));
    }

    public function exportExcel()
    {
        return Excel::download(new LaporanPiutangExport, 'laporan_piutang.xlsx');
    }
    
    public function exportPdf()
    {
        $laporanPiutang = LaporanPiutang::with(['jenisPajak', 'kategoriPajak'])
        ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at secara menurun
        ->get();;
    
        $pdf = Pdf::loadView('admin.laporan_piutang.pdf', compact('laporanPiutang'));
    
        // Unduh file PDF
        return $pdf->download('laporan_piutang.pdf');
    }
    
}
