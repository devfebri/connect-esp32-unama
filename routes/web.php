<?php

use App\Http\Controllers\SensorReadingController;
use Illuminate\Support\Facades\Route;


Route::get('/report', [SensorReadingController::class, 'report'])->name('sensor.report');
Route::get('/', fn() => view('welcome'))->name('home');
