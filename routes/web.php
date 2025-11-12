<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sales/{sale}/print', [PrintController::class, 'printStruk'])->name('sales.print');
