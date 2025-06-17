<?php

use Osiset\ShopifyApp\Util;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

if (!config('shopify-app.appbridge_enabled')) {
    Route::match(
        ['GET', 'POST'],
        '/authenticate',
        AuthenticatedSessionController::class . '@authenticate'
    )
        ->name('authenticate');
    Route::get(
        '/authenticate/token',
        AuthenticatedSessionController::class . '@authenticate'
    )
        ->middleware(['verify.shopify'])
        ->name(Util::getShopifyConfig('route_names.authenticate.token'));
}

Route::group(['middleware' => ['verify.embedded', 'verify.shopify']], function () {

    Route::get('/', function () {
        return null;
    })->name('home');

});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'orderSeacrhfilter'])->name('search');

});

require __DIR__ . '/auth.php';
