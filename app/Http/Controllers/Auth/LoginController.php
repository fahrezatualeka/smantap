<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        // Middleware untuk memastikan hanya user yang belum login yang bisa mengakses halaman login
        $this->middleware('guest')->except('logout');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $user = User::where('username', $request->username)->first();
        $usernameError = false;
        $passwordError = false;
    
        if (!$user) {
            $usernameError = true;
        } else {
            if (!Auth::attempt($credentials)) {
                $passwordError = true;
            }
        }
    
        if ($usernameError && $passwordError) {
            return back()->withErrors([
                'username' => 'Username dan password yang anda masukkan salah.',
            ]);
        } elseif ($usernameError) {
            return back()->withErrors([
                'username' => 'Username yang anda masukkan salah.',
            ])->onlyInput('username');
        } elseif ($passwordError) {
            return back()->withErrors([
                'password' => 'Password yang anda masukkan salah.',
            ])->onlyInput('username');
        }
    
        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.index')->with('success', 'Berhasil Login sebagai Admin!');
            case 'petugas_penagihan':
                return redirect()->route('petugas_penagihan.index')->with('success', 'Berhasil Login sebagai Petugas Penagihan!');
            case 'pimpinan':
                return redirect()->route('pimpinan.index')->with('success', 'Berhasil Login sebagai Pimpinan');
            default:
                Auth::logout();
                return back()->withErrors(['username' => 'Role tidak dikenali.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Logged out successfully');
    }
}
