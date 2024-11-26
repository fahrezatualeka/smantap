<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'unique:users,email',
                'not_regex:/\s/'
            ],
            'username' => [
                'required',
                'unique:users,username',
                'regex:/^[a-z0-9._]+$/',
                'not_regex:/\s/'
            ],
            'password' => 'required|min:5',
            'no_telepon' => [
                'required',
                'unique:users,no_telepon'
            ],
            'alamat' => 'required',
            'kode' => 'required'
        ], [
            'name.required' => 'Nama tidak boleh Kosong.',
            'email.required' => 'Email tidak boleh Kosong.',
            'email.unique' => 'Email sudah Digunakan.',
            'email.not_regex' => 'Email tidak boleh mengandung Spasi.',
            'username.required' => 'Username tidak boleh Kosong.',
            'username.unique' => 'Username sudah Digunakan.',
            'username.regex' => 'Username hanya boleh terdiri dari Huruf Kecil, Angka, Titik, dan Underscore.',
            'username.not_regex' => 'Username tidak boleh mengandung Spasi.',
            'password.required' => 'Password tidak boleh Kosong.',
            'password.min' => 'Password minimal terdiri dari 5 Karakter.',
            'no_telepon.required' => 'Nomor Telepon tidak boleh Kosong.',
            'no_telepon.unique' => 'Nomor Telepon sudah Digunakan.',
            'alamat.required' => 'Alamat tidak boleh Kosong.',
            'kode.required' => 'Kode tidak boleh Kosong.',
        ]);
    
        // Buat akun baru
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = strtolower($request->username);
        $user->password = bcrypt($request->password);
        $user->no_telepon = $request->no_telepon;
        $user->alamat = $request->alamat;
        $user->kode = $request->kode;
        $user->save();
    
        // Redirect ke halaman login setelah pembuatan akun
        return redirect('/')->with('success', 'Akun berhasil di Buat. Silakan Login.');
    }    
}