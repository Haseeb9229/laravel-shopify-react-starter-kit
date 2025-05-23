<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['verify.embedded', 'verify.shopify']], function () {

    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('home');

});

require __DIR__ . '/auth.php';
