<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::group(['middleware' => ['verify.embedded', 'verify.shopify']], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/search', [DashboardController::class, 'orderSeacrhfilter'])->name('search');

});

require __DIR__ . '/auth.php';
