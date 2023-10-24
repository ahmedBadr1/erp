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

Route::group(['prefix'=>'dev','as'=>'dev.'],function (){
    Route::get('/', function () {
        return 'dev';
    });

}) ;
