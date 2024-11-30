<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DataUserController extends Controller
{
    public function index()
    {
        $data = User::all(); // Ambil semua data pengguna
        return view('admin.data_user.data', compact('data'));
    }

    public function filter(Request $request)
    {
        $query = User::query();
    
        // Filter berdasarkan role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
    
        // Filter berdasarkan pembagian zonasi
        if ($request->has('pembagian_zonasi') && $request->pembagian_zonasi != '') {
            $query->where('pembagian_zonasi', $request->pembagian_zonasi);
        }
    
        // Pencarian berdasarkan nama atau username
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%')
                  ->orWhere('nomor_telepon', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%');
                //   ->orWhere('role', 'LIKE', '%' . $search . '%')
                //   ->orWhere('pembagian_zonasi', 'LIKE', '%' . $search . '%');
            });
        }
    
        $data = $query->get();
        return view('admin.data_user.data', compact('data'));
    }
    

    public function create()
    {
        return view('admin.data_user.add');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|min:3',
            'nomor_telepon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'role' => 'required|in:admin,petugas_penagihan,pimpinan',
            // 'pembagian_zonasi' => 'nullable|integer|exists:zonasi,id',
            'pembagian_zonasi' => 'nullable|integer',
        ]);
    
        $data['password'] = Hash::make($data['password']);
        
        if ($data['role'] === 'admin') {
            $data['pembagian_zonasi'] = null;
        }
    
        try {
            User::create($data);
            return redirect()->route('admin.data_user.data')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan ke database: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Kesalahan umum: ' . $e->getMessage()])->withInput();
        }
    }
    
    

//     public function edit($id)
// {
//     $user = User::findOrFail($id); // Mencari pengguna berdasarkan ID
//     return view('admin.data_user.edit', compact('user')); // Mengirim data pengguna ke view edit
// }

// public function update(Request $request, $id)
// {
//     $data = $request->validate([
//         'nama' => 'required',
//         'username' => 'required',
//         'password' => 'nullable|min:3', // Password bisa null
//         'nomor_telepon' => 'required',
//         'alamat' => 'required',
//         'role' => 'required|in:admin,petugas_penagihan',
//         'pembagian_zonasi' => 'nullable|integer',
//     ]);

//     $user = User::findOrFail($id); // Mencari pengguna berdasarkan ID

//     // Update hanya jika password diisi
//     if ($request->filled('password')) {
//         $data['password'] = Hash::make($data['password']);
//     } else {
//         unset($data['password']); // Menghapus password dari array jika tidak diisi
//     }

//     // Jika role adalah admin, set pembagian_zonasi ke null
//     if ($data['role'] === 'admin') {
//         $data['pembagian_zonasi'] = null;
//     }

//     $user->update($data); // Memperbarui data pengguna

//     return redirect()->route('admin.data_user.data')->with('success', 'Data berhasil diperbarui!'); // Kembali dengan pesan sukses
// }

    

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.admin.data_user.data')->with('success', 'Data berhasil di Hapus');
    }
}