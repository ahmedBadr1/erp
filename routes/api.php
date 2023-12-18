<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['cors', 'json.response']], function () {

    Route::group(['middleware' => ['guest:api']], function () {
        Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
        Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('reg', [\App\Http\Controllers\Api\AuthController::class, 'reg']);
        Route::get('docs', [\App\Http\Controllers\Api\AuthController::class, 'docs'])->name('docs');

    });

    Route::group(['middleware' => ['auth:api',]], function () {

        Route::post('invite', [\App\Http\Controllers\Api\Hr\UsersController::class, 'process'])->name('process');

        Route::post('logout', [\App\Http\Controllers\Api\DashboardController::class, 'logout']);

        Route::post('profile/', [\App\Http\Controllers\Api\DashboardController::class, 'profile'])->name('profile');
        Route::post('profile/update', [\App\Http\Controllers\Api\DashboardController::class, 'profileUpdate'])->name('profile-update');
        Route::post('notifications/', [\App\Http\Controllers\Api\DashboardController::class, 'notifications'])->name('notifications');
        Route::post('notifications/read', [\App\Http\Controllers\Api\DashboardController::class, 'markAsRead'])->name('notifications');

        Route::post('unread-notifications/', [\App\Http\Controllers\Api\DashboardController::class, 'unreadNotifications'])->name('unread-notifications');

        Route::get('roles/permissions', [\App\Http\Controllers\Api\Hr\RolesController::class, 'permissions'])->name('roles.permissions');
        Route::post('roles/permissions', [\App\Http\Controllers\Api\Hr\RolesController::class, 'permissionsCreate']);
        Route::delete('permission/delete/{id}', [\App\Http\Controllers\Api\Hr\RolesController::class, 'permissionsDelete'])->name('permission.delete');

        Route::group(['prefix' => 'users', 'middleware' => ['auth:api', ]], function () {
    Route::post('/', [\App\Http\Controllers\Api\Hr\UsersController::class, 'index']);
});

//   Route::resource('employees',\App\Http\Controllers\Api\Hr\EmployeesController::class);


    });
//

//
//
//
//Route::group(['prefix' => '', 'middleware' => []], function () {
//    Route::post('login', [UsersController::class, 'login']);
//    Route::get('{user}', [UsersController::class, 'get']);
//    Route::get('photo/{user}', [UsersController::class, 'photo']);
//});
//
///*
//|--------------------------------------------------------------------------
//| Hr API
//|--------------------------------------------------------------------------
//*/
//

//Route::group(['prefix' => 'employees', 'middleware' => ['auth:api', ]], function () {
//
//    Route::post('', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'index']);
//    Route::put('{employee?}', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'put']);
//
//});

///*
//|--------------------------------------------------------------------------
//| Settings API
//|--------------------------------------------------------------------------
//*/
//
//Route::group(['prefix' => 'settings', 'middleware' => ['auth:api', ]], function () {
//    Route::post('', [SettingController::class, 'settings']);
//    Route::put('{setting?}', [SettingController::class, 'put']);
//    Route::get('{setting}/{details?}', [SettingController::class, 'get']);
//    Route::delete('{setting}', [SettingController::class, 'remove']);
//});

    /*
    |--------------------------------------------------------------------------
    | Roles API
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'roles', 'middleware' => ['auth:api']], function () {

        Route::post('', [RolesController::class, 'roles']);
        Route::get('{role?}', [RolesController::class, 'get']);
        Route::put('{role?}', [RolesController::class, 'put']);
        Route::delete('{role}', [RolesController::class, 'delete']);
        Route::put('user/{user}', [RolesController::class, 'sync']);
        Route::get('user/{user}', [RolesController::class, 'user']);

    });


    /*
    |--------------------------------------------------------------------------
    | Employees API
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'employees', 'middleware' => ['auth:api',]], function () {
        Route::post('', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'list']);
        Route::get('/create', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'create']);
        Route::get('access/{employee?}', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'show']);
        Route::put('{employee?}', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'put']);
        Route::delete('{employee}', [\App\Http\Controllers\Api\Hr\EmployeesController::class, 'destroy']);
    });


    /*
    |--------------------------------------------------------------------------
    | Accounting API
    |--------------------------------------------------------------------------
    */

    Route::group(['middleware' => ['auth:api',]], function () {
        Route::post('accounts/tree', [\App\Http\Controllers\Api\Accounting\AccountsController::class, 'tree']);
        Route::post('entries', [\App\Http\Controllers\Api\Accounting\EntriesController::class, 'index']);
        Route::get('entries/create', [\App\Http\Controllers\Api\Accounting\EntriesController::class, 'create']);
        Route::put('entries/store', [\App\Http\Controllers\Api\Accounting\EntriesController::class, 'store']);
    });


    /*
    |--------------------------------------------------------------------------
    | Purchases API
    |--------------------------------------------------------------------------
    */

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('bills', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'index']);
        Route::get('bills/create', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'create']);
        Route::post('bills/search-vendor', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'searchVendor']);
        Route::post('bills/search-item', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'searchProduct']);

        Route::put('bills/store', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'store']);

        Route::post('payments', [\App\Http\Controllers\Api\Purchases\PaymentsController::class, 'index']);
        Route::get('payments/create', [\App\Http\Controllers\Api\Purchases\PaymentsController::class, 'create']);
        Route::put('payments/store', [\App\Http\Controllers\Api\Purchases\PaymentsController::class, 'store']);

        Route::post('vendors', [\App\Http\Controllers\Api\Purchases\VendorsController::class, 'list']);
        Route::get('vendors/create', [\App\Http\Controllers\Api\Purchases\VendorsController::class, 'create']);
        Route::post('vendors/get-states', [\App\Http\Controllers\Api\Purchases\VendorsController::class, 'getStates']);
        Route::put('vendors/{vendor?}', [\App\Http\Controllers\Api\Purchases\VendorsController::class, 'store']);
        Route::get('vendors/{id}', [\App\Http\Controllers\Api\Purchases\VendorsController::class, 'show']);


        Route::post('contacts', [\App\Http\Controllers\Api\Purchases\VendorsController::class, 'storeContact']);

    });

});
