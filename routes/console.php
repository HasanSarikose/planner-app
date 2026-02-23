<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Task;
use App\Mail\DailyTaskReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

Schedule::call(function () {
    $today = Carbon::today()->toDateString();

    // Başlangıç ve bitiş tarihi aralığına bugünü kapsayan görevleri bul
    $todayTasks = Task::whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->get();

    // Eğer bugün yapılacak bir iş varsa mail at
    if ($todayTasks->count() > 0) {
        // BURAYA KENDİ E-POSTA ADRESİNİ YAZ
        Mail::to('hasansarikose33@gmail.com')->send(new DailyTaskReminder($todayTasks));
    }
})->dailyAt('08:00');
