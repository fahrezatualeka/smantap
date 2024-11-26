<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPajak;

class KategoriPajakController extends Controller
{
    public function index()
    {
        // Mengambil data kategori pajak beserta jenis pajaknya
        $data = KategoriPajak::with('JenisPajak')->get();
        return view('admin.kategori_pajak.data', compact('data'));
    }

    public function filter(Request $request)
    {
        $query = KategoriPajak::query();    
        
        // Filter berdasarkan jenis pajak (jenis_pajak_id)
        if ($request->has('jenis_pajak_id') && $request->jenis_pajak_id != '') {
            $query->where('jenis_pajak_id', $request->jenis_pajak_id);
        }
    
        // Pencarian berdasarkan nama kategori pajak dan nama jenis pajak
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Pencarian di kolom kategori_pajak
                $q->where('kategoripajak', 'LIKE', '%' . $search . '%')
                  // Pencarian di kolom jenispajak di tabel jenis_pajaks
                  ->orWhereHas('JenisPajak', function ($q) use ($search) {
                      $q->where('jenispajak', 'LIKE', '%' . $search . '%'); // Kolom 'jenispajak' di tabel jenis_pajak
                  });
            });
        }
        
        // Mendapatkan data setelah difilter
        $data = $query->get();
        return view('admin.kategori_pajak.data', compact('data'));
    }
    
    
    

    public function create()
    {
        // Mengambil semua jenis pajak
        $jenisPajaks = \App\Models\JenisPajak::all();
        return view('admin.kategori_pajak.add', compact('jenisPajaks'));
    }

    public function store(Request $request)
    {
        // Validasi input untuk memastikan 'jenis_pajak_id' ada dan valid
        $data = $request->validate([
            'jenis_pajak_id' => 'required|exists:jenispajak,id',  // Validasi tambahan untuk memastikan 'jenis_pajak_id' ada di tabel 'jenispajak'
            'kategoripajak' => 'required',
        ]);
        
        // Menyimpan data kategori pajak dengan 'jenis_pajak_id' dan 'kategoripajak'
        KategoriPajak::create($data);
        
        return redirect()->route('admin.kategori_pajak.data')->with('success', 'Data berhasil ditambahkan!');
    }
    

    public function edit($id)
    {
        $kategoriPajak = KategoriPajak::findOrFail($id); // Mendapatkan data kategori pajak berdasarkan ID
        $jenisPajaks = \App\Models\JenisPajak::all();  // Ambil semua jenis pajak untuk dropdown
        return view('admin.kategori_pajak.edit', compact('kategoriPajak', 'jenisPajaks'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'kategoripajak' => 'required',
            'jenis_pajak_id' => 'required|exists:jenispajak,id',  // Validasi 'jenis_pajak_id'
        ]);
    
        $kategoriPajak = KategoriPajak::findOrFail($id);
        $kategoriPajak->update($data);
    
        return redirect()->route('admin.kategori_pajak.data')->with('success', 'Data berhasil diperbarui!');
    }
    

    public function destroy($id)
    {
        KategoriPajak::findOrFail($id)->delete();
        return redirect()->route('admin.kategori_pajak.data')->with('success', 'Data berhasil dihapus!');
    }
}