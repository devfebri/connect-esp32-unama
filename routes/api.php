<?php

use App\Http\Controllers\SensorReadingController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/esp32/readings', [SensorReadingController::class, 'index']);
Route::get('/esp32/readings/all', [SensorReadingController::class, 'all']);
Route::post('/esp32/readings', [SensorReadingController::class, 'store']);

Route::post('/esp32/import-csv', [SensorReadingController::class, 'import']);

// Notifikasi Telegram — dibatasi 1 request per 10 menit per IP agar tidak spam
Route::middleware('throttle:6,10')->post('/telegram/notify-offline', [TelegramController::class, 'notifyOffline']);
