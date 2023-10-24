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
    Route::get('login',[\App\Http\Controllers\Admin\Auth\LoginController::class,'showLoginForm'])->name('showLogin');
    Route::post('login/check',[\App\Http\Controllers\Admin\Auth\LoginController::class,'login'])->name('login');
    Route::get('register',[\App\Http\Controllers\Admin\Auth\LoginController::class,'register'])->name('register');

    Route::group(['middleware'=>'auth'],function (){
        Route::get('/',[\App\Http\Controllers\Admin\DashboardController::class,'dashboard'])->name('dashboard');
        Route::get('logout',[\App\Http\Controllers\Admin\Auth\LoginController::class,'logout'])->name('logout');
        Route::get('/profile',[\App\Http\Controllers\Admin\DashboardController::class,'profile'])->name('profile');


    }) ;
}) ;
