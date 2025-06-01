<?php

use App\Http\Controllers\DashboardController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['verify.embedded', 'verify.shopify']], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('home');

});

require __DIR__ . '/auth.php';
