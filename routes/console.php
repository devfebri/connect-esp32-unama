<?php

use App\Console\Commands\CheckSensorOffline;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler: Cek Status Sensor Offline
|--------------------------------------------------------------------------
|
| Jalankan pengecekan setiap 5 menit. Jika data sensor tidak masuk
| lebih dari 5 menit, kirim notifikasi ke grup Telegram.
|
*/
Schedule::command(CheckSensorOffline::class)->everyFiveMinutes();
