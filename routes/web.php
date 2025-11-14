<?php

use App\Http\Controllers\PrintController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/sales/print/{invoice}', [PrintController::class, 'printStruk'])->name('sales.print');
    Route::resource('sales', SaleController::class);
});

