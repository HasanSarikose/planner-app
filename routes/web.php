<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

Route::get('/', [TaskController::class, 'index']); // Arayüzü açan rota (Herkese açık)

// Giriş ve Çıkış İşlemleri
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// GÜVENLİK DUVARI: Sadece giriş yapanlar ("auth") bu verilere erişebilir
Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'getTasks']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']); // Güncelleme rotası
});

Route::get('/sifre-sifirla', function() {
    $user = \App\Models\User::where('name', 'HSAR')->first();
    if($user) {
        $user->password = \Illuminate\Support\Facades\Hash::make('SrksHsn33');
        $user->save();
        return '✅ Şifre BAŞARIYLA SrksHsn33 olarak güncellendi!';
    }
    return 'Kullanıcı bulunamadı.';
});

Route::get('/mail-test', function () {
    // BURAYA KENDİ MAİL ADRESİNİ YAZMAYI UNUTMA!
    $aliciMail = 'hasansarikose33@gmail.com';

    \Illuminate\Support\Facades\Mail::raw('Eğer bu mesajı okuyorsan, Laravel başarıyla Gmail hesabına bağlanıp asistan olarak mail atmayı başarmış demektir! 🎉', function ($message) use ($aliciMail) {
        $message->to($aliciMail)
            ->subject('🚀 Planner Sistem Testi');
    });

    return '✅ Mail komutu sunucuya iletildi! Lütfen Gmail kutunu (ve gereksiz/spam klasörünü) kontrol et.';
});
