<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/esp32/readings', [SensorReadingController::class, 'index']);
Route::get('/esp32/readings/all', [SensorReadingController::class, 'all']);
Route::post('/esp32/readings', [SensorReadingController::class, 'store']);
