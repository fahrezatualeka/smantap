<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wp;
use App\Models\Pgh_airbawahtanah;
use App\Models\Pgh_hiburan;
use App\Models\Pgh_hotel;
use App\Models\Pgh_logambatuan;
use App\Models\Pgh_parkir;
use App\Models\Pgh_reklame;
use App\Models\Pgh_restoran;
use App\Models\Pgh_sampah;
use PDF;
use App\Exports\WpExport;
use Maatwebsite\Excel\Facades\Excel;

class WpController extends Controller
{
    public function index()
    {
        $data = Wp::all();
        return view('wp.data', compact('data'));
    }

    public function create()
    {
        return view('wp.add');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'npwpd' => 'required',
            'nama_pajak' => 'required',
            'nama_kelola' => 'required',
            'jenis' => 'required|in:airbawahtanah,hiburan,hotel,logambatuan,parkir,reklame,restoran,sampah',
            'no_telepon' => 'required',
            'alamat' => 'required',
            'omset' => 'required|numeric',
            'pajak' => 'required|numeric',

        ]);

    
        Wp::create($data);
    
        return redirect()->route('wp.formSuccess')->with('success', 'Data berhasil ditambahkan!');
    }

    public function formSuccess()
    {
        return view('wp.formSuccess');
    }

    public function edit($id)
    {
        $wp = Wp::findOrFail($id);
        return view('wp.edit', compact('wp'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'npwpd' => 'required',
            'nama_pajak' => 'required',
            'nama_kelola' => 'required',
            'jenis' => 'required|in:airbawahtanah,hiburan,hotel,logambatuan,parkir,reklame,restoran,sampah',
            'no_telepon' => 'required',
            'alamat' => 'required',
            'omset' => 'required|numeric',
            'pajak' => 'required|numeric'
        ]);
    
        $wp = Wp::findOrFail($id);
        $wp->update($data);
    
        return redirect()->route('wp.data')->with('success', 'Data berhasil diubah!');
    }

    public function search(Request $request)
    {
        $npwpd = $request->query('npwpd');
        $wp = Wp::where('npwpd', $npwpd)->first();

        if ($wp) {
            return response()->json($wp);
        } else {
            return response()->json(null, 404); // Kembalikan status 404 jika tidak ditemukan
        }
    }

    public function destroy($id)
    {
        $wp = Wp::findOrFail($id);
        
        // Hapus semua data penagihan yang terkait dengan WP ini
        if ($wp->jenis == 'airbawahtanah') {
            Pgh_airbawahtanah::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'hiburan') {
            Pgh_hiburan::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'hotel') {
            Pgh_hotel::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'logambatuan') {
            Pgh_logambatuan::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'parkir') {
            Pgh_parkir::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'reklame') {
            Pgh_reklame::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'restoran') {
            Pgh_restoran::where('npwpd', $wp->npwpd)->delete();
        } elseif ($wp->jenis == 'sampah') {
            Pgh_sampah::where('npwpd', $wp->npwpd)->delete();
        }
        
        // Hapus data WP
        $wp->delete();
    
        return redirect()->route('wp.data')->with('success', 'Data berhasil dihapus!');
    }    

    public function filterJenis(Request $request)
    {
        $jenis = $request->input('jenis');
    
        // Simpan filter jenis ke dalam session
        $request->session()->put('jenis', $jenis);
    
        if ($jenis == 'semua') {
            $data = Wp::all();
        } else {
            $data = Wp::where('jenis', $jenis)->get();
        }
    
        return view('wp.data', compact('data'));
    }
    
    public function cetakRekap(Request $request)
    {
        return redirect()->route('wp.cetakRekapExcel');
    }    
}