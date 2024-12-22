<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\KelolaPesanWhatsapp;

    
    class KelolaPesanWhatsappController extends Controller
    {
            public function index()
            {
                $datawhatsapp = KelolaPesanWhatsapp::all();
                return view('admin.kelola_pesan_whatsapp.data', compact('datawhatsapp'));
            }
            public function create()
            {
                return view('admin.kelola_pesan_whatsapp.add');
            }

            public function store(Request $request)
            {
                $request->validate([
                    'jenis_pesan' => 'required|in:Pengiriman Pesan dari Petugas Penagihan,Pengiriman Pesan dari Admin',
                    'deskripsi' => 'required',
                ]);
                
                // Simpan data baru
                KelolaPesanWhatsapp::create([
                    'jenis_pesan' => $request->jenis_pesan,  // Mengganti jenis_pajak menjadi jenis_pesan
                    'deskripsi' => $request->deskripsi,
                ]);
                
                // Redirect dengan pesan sukses
                return redirect()->route('admin.kelola_pesan_whatsapp.data')->with('success', 'Data berhasil ditambahkan!');
            }
            
            public function edit($id)
            {
                $datawhatsapp = KelolaPesanWhatsapp::findOrFail($id);
                return view('admin.kelola_pesan_whatsapp.edit', compact('datawhatsapp'));
            }

            public function update(Request $request, $id)
            {
                $request->validate([
                    'deskripsi' => 'required',
                ]);
                
                // Temukan data yang ingin diupdate
                $datawhatsapp = KelolaPesanWhatsapp::findOrFail($id);
            
                // Update data
                $datawhatsapp->update([
                    'jenis_pesan' => $request->jenis_pesan,  // Tidak perlu 'readonly' di form, jika Anda ingin memodifikasi jenis_pesan, hapus readonly
                    'deskripsi' => $request->deskripsi,
                ]);
                
                // Redirect dengan pesan sukses
                return redirect()->route('admin.kelola_pesan_whatsapp.data')->with('success', 'Data berhasil diupdate!');
            }
    }