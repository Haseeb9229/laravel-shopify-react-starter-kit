<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Models\orders\Order;
use Illuminate\Http\Request;


Route::group(['middleware' => ['verify.embedded', 'verify.shopify']], function () {

    Route::get('/', [DashboardController::class, 'home'])->name('home');

    Route::get("/search", [DashboardController::class, 'orderSeacrhfilter'])->name('search');

});

// Route::get("/search" , function(Request $request)  {
//     $query = $request->query("query");
//     $results = Order::where('name', 'LIKE', "%{$query}%")->get();

//     return response()->json($results);
// });


require __DIR__ . '/auth.php';
