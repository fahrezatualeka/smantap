<?php

namespace App\Http\Controllers;
use App\Models\DataWajibPajak;
use App\Models\DataPiutang;
use App\Models\JenisPajak;
use App\Models\DataPenagihan;
use App\Models\DataTransfer;
use App\Models\DataTunai;
use App\Models\DataKonfirmasi;
use App\Models\DataPenutupan;
use App\Models\User;
use App\Models\LaporanTransfer;
use App\Models\LaporanTunai;
use App\Models\LaporanKonfirmasi;
use App\Models\LaporanPenutupan;
use Illuminate\Http\Request;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        $datawajibpajak = DataWajibPajak::count();
        $datapiutang = DataPiutang::count();
        $jenispajak = JenisPajak::count();
        $laporantransfer = LaporanTransfer::count();
        $jumlahtransfer = LaporanTransfer::whereYear('created_at', date('Y'))->sum('jumlah_pembayaran');
        $laporantunai = LaporanTunai::count();
        $jumlahtunai = LaporanTunai::whereYear('created_at', date('Y'))->sum('jumlah_pembayaran');
        $laporankonfirmasi = LaporanKonfirmasi::count();

        $currentYear = 2024;
        $grafikTransfer = [];
        $grafikTunai = [];
    
        foreach (range(1, 12) as $bulan) {
            $jumlahTransfer = LaporanTransfer::whereYear('created_at', $currentYear)
                                             ->whereMonth('created_at', $bulan)
                                             ->sum('jumlah_pembayaran');
    
            $jumlahTunai = LaporanTunai::whereYear('created_at', $currentYear)
                                       ->whereMonth('created_at', $bulan)
                                       ->sum('jumlah_pembayaran');
    
            $grafikTransfer[] = $jumlahTransfer > 0 ? $jumlahTransfer : 0;
            $grafikTunai[] = $jumlahTunai > 0 ? $jumlahTunai : 0;
        }
    
        $datapiutang1 = DataPenagihan::where('zona', 1)->count();
        $datapiutang2 = DataPenagihan::where('zona', 2)->count();
        $datapiutang3 = DataPenagihan::where('zona', 3)->count();
        $datapiutang4 = DataPenagihan::where('zona', 4)->count();
        $datapiutang = DataPiutang::count();

        $laporantransfer1 = DataTransfer::where('zona', 1)->count();
        $laporantransfer2 = DataTransfer::where('zona', 2)->count();
        $laporantransfer3 = DataTransfer::where('zona', 3)->count();
        $laporantransfer4 = DataTransfer::where('zona', 4)->count();
        $laporantransfer = LaporanTransfer::count();

        $laporantunai1 = DataTunai::where('zona', 1)->count();
        $laporantunai2 = DataTunai::where('zona', 2)->count();
        $laporantunai3 = DataTunai::where('zona', 3)->count();
        $laporantunai4 = DataTunai::where('zona', 4)->count();
        $laporantunai = LaporanTunai::count();

        $laporankonfirmasi1 = DataKonfirmasi::where('zona', 1)->count();
        $laporankonfirmasi2 = DataKonfirmasi::where('zona', 2)->count();
        $laporankonfirmasi3 = DataKonfirmasi::where('zona', 3)->count();
        $laporankonfirmasi4 = DataKonfirmasi::where('zona', 4)->count();
        $laporankonfirmasi = LaporanKonfirmasi::count();

        $laporanpenutupan1 = DataPenutupan::where('zona', 1)->count();
        $laporanpenutupan2 = DataPenutupan::where('zona', 2)->count();
        $laporanpenutupan3 = DataPenutupan::where('zona', 3)->count();
        $laporanpenutupan4 = DataPenutupan::where('zona', 4)->count();
        $laporanpenutupan = LaporanPenutupan::count();


        $lingkaranpiutang = DataPiutang::count();
        $lingkarantransfer = LaporanTransfer::count();
        $lingkarantunai = LaporanTunai::count();
        $lingkarankonfirmasi = LaporanKonfirmasi::count();
        $lingkaranpenutupan = LaporanPenutupan::count();

        $datauser = User::count();

    
        return view('admin.dashboard', compact(
            'datawajibpajak',
            'datapiutang',
            'datapiutang1',
            'datapiutang2',
            'datapiutang3',
            'datapiutang4',
            'jenispajak',
            'datauser',
            'laporantransfer',
            'jumlahtransfer',
            'laporantunai',
            'jumlahtunai',
            'laporankonfirmasi',
            'laporanpenutupan',
            'grafikTransfer',
            'grafikTunai',
            'lingkaranpiutang',
            'lingkarantransfer',
            'lingkarantunai',
            'lingkarankonfirmasi',
            'lingkaranpenutupan',
            'laporantransfer1',
            'laporantransfer2',
            'laporantransfer3',
            'laporantransfer4',
            'laporantunai1',
            'laporantunai2',
            'laporantunai3',
            'laporantunai4',
            'laporankonfirmasi1',
            'laporankonfirmasi2',
            'laporankonfirmasi3',
            'laporankonfirmasi4',
            'laporanpenutupan1',
            'laporanpenutupan2',
            'laporanpenutupan3',
            'laporanpenutupan4',

        ));
    }
}