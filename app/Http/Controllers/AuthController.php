<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Memproses data login dari form
     */
    public function loginPost(Request $request)
    {
        // 1. Validasi input dari user (Keduanya tetap WAJIB)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tangkap nilai checkbox remember me (menghasilkan true/false)
        $remember = $request->boolean('remember');

        // 2. Coba melakukan autentikasi ke database
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard'); 
        }

        // 3. Jika gagal
        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Hapus session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
