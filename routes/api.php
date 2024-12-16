<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\front\ServiceController as FrontServiceController;
use App\Http\Controllers\front\ProjectController as FrontProjectController;

//route
Route::post('/authenticate',[AuthenticationController::class, 'authenticate'])->name('authenticate');

Route::get('/get-services',[FrontServiceController::class, 'index']);
Route::get('/get-latest-services',[FrontServiceController::class, 'latestServices']);

Route::get('/get-projects',[FrontProjectController::class, 'index']);
Route::get('/get-latest-projects',[FrontProjectController::class, 'latestProjects']);


//Route::get('/get-articles',[FrontProjectController::class, 'index']);
//Route::get('/get-latest-articles',[FrontProjectController::class, 'latestProjects']);


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
    Route::put('/services/{id}',[ServiceController::class, 'update']);
    Route::get('/services/{id}',[ServiceController::class, 'show']);
    Route::delete('/services/{id}',[ServiceController::class, 'destroy']);

    //projet
    Route::get('/projects',[ProjectController::class, 'index']);
    Route::post('/projects',[ProjectController::class, 'store']);
    Route::put('/projects/{id}',[ProjectController::class, 'update']);
    Route::get('/projects/{id}',[ProjectController::class, 'show']);
    Route::delete('/projects/{id}',[ProjectController::class, 'destroy']);

    //projet
    Route::get('/articles',[ArticleController::class, 'index']);
    Route::post('/articles',[ArticleController::class, 'store']);
    Route::put('/articles/{id}',[ArticleController::class, 'update']);
    Route::get('/articles/{id}',[ArticleController::class, 'show']);
    Route::delete('/articles/{id}',[ArticleController::class, 'destroy']);

    //temp image upload et installation de composer require intervention/image
    Route::post('/temp-images',[TempImageController::class, 'store']);



});
