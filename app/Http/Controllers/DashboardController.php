<?php

namespace App\Http\Controllers;
// use App\Models\DataPiutang;
use App\Models\DataZonasi;
use App\Models\DataWajibPajak;
use App\Models\DataPenagihan;
use App\Models\DataPenetapan;
use App\Models\KategoriPajak;
use App\Models\JenisPajak;
use App\Models\LaporanPelunasan;
use App\Models\LaporanPenagihan;
use App\Models\LaporanPiutang;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        // return view('admin.dashboard');
    
        // $currentMonth = Carbon::now()->month;
        // $currentYear = Carbon::now()->year;
    

        
        
        
        
        $datawajibpajak = DataWajibPajak::count();
        
        // $datapenetapan = DataPenetapan::count();
        $jumlah_penetapan_tahun = DataPenetapan::whereYear('created_at', date('Y'))->sum('jumlah_penagihan');
        // $jumlah_penetapan_bulan = DataPenetapan::whereYear('created_at', date('Y'))
        // ->whereMonth('created_at', date('m'))
        // ->sum('jumlah_penagihan');
    
    
        // $laporanpelunasan = LaporanPelunasan::count();
        $jumlah_pelunasan_tahun = LaporanPelunasan::whereYear('created_at', date('Y'))->sum('jumlah_penagihan');
        // $jumlah_pelunasan_bulan = LaporanPelunasan::whereYear('created_at', date('m'))->sum('jumlah_penagihan');

        
        // $datapiutang = DataPiutang::count();
        $jumlah_piutang_tahun = LaporanPiutang::whereYear('created_at', date('Y'))->sum('jumlah_penagihan');
        // $jumlah_piutang_bulan = DataPiutang::whereYear('created_at', date('m'))->sum('jumlah_penagihan');
        
        
        $jenispajak = JenisPajak::count();

        $kategoripajak = KategoriPajak::count();
        
        $admin = User::where('role', 'admin')->count();

        $p1 = User::where('role', 'petugas_penagihan')->where('pembagian_zonasi', 1)->count();
        $p2 = User::where('role', 'petugas_penagihan')->where('pembagian_zonasi', 2)->count();
        $p3 = User::where('role', 'petugas_penagihan')->where('pembagian_zonasi', 3)->count();
        $p4 = User::where('role', 'petugas_penagihan')->where('pembagian_zonasi', 4)->count();
        $petugaspenagihan = User::where('role', 'petugas_penagihan')->count();

        $pimpinan = User::where('role', 'pimpinan')->count();


        return view('admin.dashboard', compact('datawajibpajak', 'jumlah_penetapan_tahun', 'jumlah_pelunasan_tahun', 'jumlah_piutang_tahun', 'jenispajak', 'kategoripajak', 
        'admin', 'p1', 'p2', 'p3', 'p4', 'petugaspenagihan', 'pimpinan'));
    }
}