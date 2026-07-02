<?php

use App\Console\Commands\CheckSensorOffline;
use App\Http\Controllers\SensorReadingController;
use App\Http\Controllers\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/esp32/readings', [SensorReadingController::class, 'index']);
Route::get('/esp32/readings/all', [SensorReadingController::class, 'all']);
Route::post('/esp32/readings', [SensorReadingController::class, 'store']);

Route::post('/esp32/import-csv', [SensorReadingController::class, 'import']);

// Notifikasi Telegram — dibatasi 1 request per 10 menit per IP agar tidak spam
Route::middleware('throttle:6,10')->post('/telegram/notify-offline', [TelegramController::class, 'notifyOffline']);

// Webhook Telegram Bot — menerima update dari Telegram (perintah /status, dll.)
Route::post('/telegram/webhook', [TelegramController::class, 'handleWebhook']);

// Endpoint untuk cron job eksternal (cron-job.org)
// Aman dengan token rahasia di query string: ?token=...
Route::get('/cron/check-sensor', function (Request $request) {
    $validToken = config('app.scheduler_token');

    if (! $validToken || $request->query('token') !== $validToken) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    Artisan::call(CheckSensorOffline::class);
    $output = trim(Artisan::output());

    return response()->json([
        'status' => 'ok',
        'message' => $output ?: 'Pengecekan selesai.',
        'time' => now()->toDateTimeString(),
    ]);
});
