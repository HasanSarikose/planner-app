<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Kullanıcı adı olarak veritabanındaki "name" sütununu kullanıyoruz
        $credentials = [
            'name' => $request->username,
            'password' => $request->password
        ];

        // Laravel şifreyi kendi çözer ve kontrol eder
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Oturumu başlat
            return response()->json(['success' => true]);
        }

        // Hatalıysa 401 (Yetkisiz) kodu dön
        return response()->json(['success' => false], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }
}
