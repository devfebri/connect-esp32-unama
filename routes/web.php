<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Support\Facades\Route;



Route::get('/monitor', [SensorReadingController::class, 'dashboard'])->name('sensor.monitor');
Route::get('/', [SensorReadingController::class, 'dashboard'])->name('sensor.monitor');
