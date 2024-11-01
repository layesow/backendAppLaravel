<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\admin\DashboardController;



//route
Route::post('/authenticate',[AuthenticationController::class, 'authenticate'])->name('authenticate');

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 */

// route groupe
Route::group(['middleware' => 'auth:sanctum'], function () {
    // protection routes
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');

    //logout
    Route::get('/logout',[AuthenticationController::class, 'logout'])->name('logout');


});
