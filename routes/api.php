<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/esp32/readings', [SensorReadingController::class, 'index']);
Route::post('/esp32/readings', [SensorReadingController::class, 'store']);
