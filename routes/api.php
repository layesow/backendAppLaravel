<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\TempImageController;

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

    //service
    Route::get('/services',[ServiceController::class, 'index']);
    Route::post('/services',[ServiceController::class, 'store']);

    //temp image upload et installation de composer require intervention/image
    Route::post('/temp-images',[TempImageController::class, 'store']);



});
