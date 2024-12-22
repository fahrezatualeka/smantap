<?php


namespace App\Http\Controllers;
use App\Models\DataWajibPajak;
use App\Models\DataPenagihan;
use App\Models\LaporanTransfer;
use App\Models\LaporanTunai;
use App\Models\DataTunai;
use App\Models\LaporanKonfirmasi;
use App\Models\LaporanPenutupan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardPetugasPenagihanController extends Controller
{
    public function index()
    {
        // Ambil zona petugas penagihan yang sedang login
        $user = auth()->user(); // Pastikan Anda sudah mengatur autentikasi
        $zonaPetugas = $user->zona; // Asumsi relasi zona di model User
        
        // Filter data berdasarkan zona
        $datapenagihan = DataPenagihan::where('zona', $zonaPetugas)->count();
        $laporantransfer = LaporanTransfer::where('zona', $zonaPetugas)->count();
        $jumlahtransfer = LaporanTransfer::where('zona', $zonaPetugas)
                                          ->whereYear('created_at', date('Y'))
                                          ->sum('jumlah_pembayaran');
        $laporantunai = LaporanTunai::where('zona', $zonaPetugas)->count();
        $jumlahtunai = LaporanTunai::where('zona', $zonaPetugas)
                                    ->whereYear('created_at', date('Y'))
                                    ->sum('jumlah_pembayaran');
        $laporankonfirmasi = LaporanKonfirmasi::where('zona', $zonaPetugas)->count();
        $laporanpenutupan = LaporanPenutupan::where('zona', $zonaPetugas)->count();

        
        $belumSetor = LaporanTunai::where('zona', $zonaPetugas)
        ->whereNull('buktisspd') // Kondisi: belum upload bukti SSPD
        ->sum('jumlah_pembayaran');

        $setorKasda = LaporanTunai::where('zona', $zonaPetugas)
        ->whereNotNull('buktisspd') // Kondisi: sudah upload bukti SSPD
        ->sum('jumlah_pembayaran');


        $currentYear = 2024;
        $grafikTransfer = [];
        $grafikTunai = [];
    
        // Grafik bulanan
        foreach (range(1, 12) as $bulan) {
            $jumlahTransfer = LaporanTransfer::where('zona', $zonaPetugas)
                                             ->whereYear('created_at', $currentYear)
                                             ->whereMonth('created_at', $bulan)
                                             ->sum('jumlah_pembayaran');
            $jumlahTunai = LaporanTunai::where('zona', $zonaPetugas)
                                       ->whereYear('created_at', $currentYear)
                                       ->whereMonth('created_at', $bulan)
                                       ->sum('jumlah_pembayaran');
    
            $grafikTransfer[] = $jumlahTransfer > 0 ? $jumlahTransfer : 0;
            $grafikTunai[] = $jumlahTunai > 0 ? $jumlahTunai : 0;
        }
    
        // Data lingkaran
        $lingkaranpenagihan = DataPenagihan::where('zona', $zonaPetugas)->count();
        $lingkarantransfer = LaporanTransfer::where('zona', $zonaPetugas)->count();
        $lingkarantunai = LaporanTunai::where('zona', $zonaPetugas)->count();
        $lingkarankonfirmasi = LaporanKonfirmasi::where('zona', $zonaPetugas)->count();
        $lingkaranpenutupan = LaporanPenutupan::where('zona', $zonaPetugas)->count();
    
        return view('petugas_penagihan.dashboard', compact(
            'datapenagihan',
            'laporantransfer',
            'jumlahtransfer',
            'laporantunai',
            'jumlahtunai',
                    'belumSetor',
        'setorKasda',
            'laporankonfirmasi',
            'laporanpenutupan',
            'grafikTransfer',
            'grafikTunai',
            'lingkaranpenagihan',
            'lingkarantransfer',
            'lingkarantunai',
            'lingkarankonfirmasi',
            'lingkaranpenutupan'
        ));
    }
}