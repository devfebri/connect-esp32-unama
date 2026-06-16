<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/monitor', [SensorReadingController::class, 'dashboard'])->name('sensor.monitor');
