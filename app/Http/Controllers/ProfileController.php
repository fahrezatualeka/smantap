<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        // Validasi hanya untuk username dan password
        $request->validate([
            'username' => 'required|unique:users,username,' . auth()->user()->id,
            'password' => 'nullable|min:3',
        ]);
    
        // Ambil data pengguna yang sedang login
        $user = Auth::user();
    
        // Update username jika ada perubahan
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
    
        // Update password jika ada perubahan
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password); // Enkripsi password baru
        }
    
        // Simpan perubahan ke database
        $user->save();
    
        // Logout setelah update dan redirect ke halaman login
        auth()->logout();
        return redirect()->route('login')->with('success', 'Profil Anda berhasil diperbarui. Silakan login kembali.');
    }
    
    
    
    
    
    
    
    
    
    
}