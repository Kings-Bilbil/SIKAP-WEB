<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Fungsi untuk melempar user ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Fungsi untuk menangani balasan dari Google
    public function handleGoogleCallback()
    {
        try {
            // PERUBAHAN 1: Tambahkan stateless() khusus untuk Vercel
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cek apakah user sudah ada, jika belum buat baru
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt('123456dummy') // Password acak karena login via Google
                ]
            );

            // Login-kan user ke dalam sistem Laravel
            Auth::login($user);

            // Arahkan ke Dashboard
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            // PERUBAHAN 2: Menampilkan pesan error ASLI dari sistem (Bukan pesan rahasia lagi)
            // Ini akan mencetak layar putih dengan tulisan error spesifik agar kita tahu pasti penyebabnya.
            dd('ERROR GOOGLE LOGIN: ' . $e->getMessage());
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