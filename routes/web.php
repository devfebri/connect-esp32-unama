<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/monitor', [SensorReadingController::class, 'dashboard'])->name('sensor.monitor');
Route::get('/report', [SensorReadingController::class, 'report'])->name('sensor.report');
Route::get('/', fn () => view('welcome'))->name('home');
