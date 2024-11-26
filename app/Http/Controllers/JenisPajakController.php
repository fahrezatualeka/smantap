<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisPajak;

class JenisPajakController extends Controller
{
    public function index()
    {
        $data = JenisPajak::all();
        return view('admin.jenis_pajak.data', compact('data'));
    }

    public function create()
    {
        return view('admin.jenis_pajak.add');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenispajak' => 'required',
        ]);

        JenisPajak::create($data);

        return redirect()->route('admin.jenis_pajak.data')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = JenisPajak::findOrFail($id); // Mendapatkan data jenis pajak berdasarkan ID
        return view('admin.jenis_pajak.edit', compact('user')); // Mengirim variabel user ke view yang benar
    }
    

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'jenispajak' => 'required',
        ]);

        $jenispajak = JenisPajak::findOrFail($id);
        $jenispajak->update($data);

        return redirect()->route('admin.jenis_pajak.data')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        JenisPajak::findOrFail($id)->delete();
    return redirect()->route('admin.jenis_pajak.data')->with('success', 'Data berhasil dihapus!');
    }
}