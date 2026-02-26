<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Mengarahkan pengguna ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Menangani kembalian (callback) dari Google setelah sukses login
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user dengan email tersebut sudah ada di database kita
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Jika user belum ada, daftarkan sebagai user baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null, // Dikosongkan karena menggunakan Google Login
                ]);
            } else {
                // Jika user sudah ada, update google_id dan avatar-nya (berjaga-jaga jika ada perubahan)
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Daftarkan session login user ke sistem Laravel (Remember = true)
            Auth::login($user, true);

            // Arahkan ke halaman dashboard
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            // Jika terjadi error (misal batal login), kembalikan ke halaman utama
            return redirect('/')->with('error', 'Gagal login menggunakan Google. Silakan coba lagi.');
        }
    }

    // Fungsi untuk Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}