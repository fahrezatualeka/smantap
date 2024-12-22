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
        $laporanPelunasan = LaporanPelunasan::with(['jenisPajak'])
        ->orderBy('created_at', 'desc')
        ->get();
    
        return view('admin.laporan_pelunasan.data', compact('laporanPelunasan'));
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'jenis_pajak_id' => 'nullable|integer|exists:jenispajak,id',
            'zona' => 'nullable|integer',
            'bulan' => 'nullable|string|max:15',
            'metode_pembayaran' => 'nullable|string',
        ]);
    
        $query = LaporanPelunasan::query();
    
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

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }
    
        $laporanPelunasan = $query->latest()->get();
    
        return view('admin.laporan_pelunasan.data', compact('laporanPelunasan'));
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