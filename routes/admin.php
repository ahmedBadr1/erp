<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dev Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Test routes for your application. These
| routes are loaded only in local environment by the RouteServiceProvider and all of them will
| be assigned to the "dev" middleware group. Make something great!
|
*/

Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
    Route::get('login',[\App\Http\Controllers\Admin\Auth\LoginController::class,'showLoginForm'])->name('login');
    Route::post('login',[\App\Http\Controllers\Admin\Auth\LoginController::class,'login']);
    Route::get('register',[\App\Http\Controllers\Admin\Auth\LoginController::class,'register'])->name('register');

    Route::group(['middleware'=>'auth'],function (){
        Route::get('/',[\App\Http\Controllers\Admin\DashboardController::class,'dashboard'])->name('dashboard');
        Route::post('logout',[\App\Http\Controllers\Admin\Auth\LoginController::class,'logout'])->name('logout');
        Route::get('/profile',[\App\Http\Controllers\Admin\DashboardController::class,'profile'])->name('profile');
        Route::get('/notifications',[\App\Http\Controllers\Admin\DashboardController::class,'notifications'])->name('notifications');
        Route::get('/help-center',[\App\Http\Controllers\Admin\DashboardController::class,'help'])->name('help');
        Route::get('/reports',[\App\Http\Controllers\Admin\DashboardController::class,'reports'])->name('reports');


        Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
            Route::get('/',[\App\Http\Controllers\Admin\DashboardController::class,'dashboard'])->name('dashboard');

        }) ;
    }) ;
}) ;
