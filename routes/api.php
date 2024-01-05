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

Route::group(['middleware' => [ 'json.response']], function () {

    Route::group(['middleware' => ['guest:api']], function () {
        Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
        Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('check', [\App\Http\Controllers\Api\AuthController::class, 'check']);
        Route::get('docs', [\App\Http\Controllers\Api\AuthController::class, 'docs'])->name('docs');

        Route::post('forget-password', [\App\Http\Controllers\Api\AuthController::class, 'forget']);
        Route::post('check/password', [\App\Http\Controllers\Api\AuthController::class, 'checkPassword']);

        Route::post('reset-password', [\App\Http\Controllers\Api\AuthController::class, 'reset']);


    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {

        Route::post('invite', [\App\Http\Controllers\Api\Hr\UsersController::class, 'invite'])->name('invite');
        Route::post('invitations', [\App\Http\Controllers\Api\Hr\UsersController::class, 'invitations'])->name('invitations');
        Route::delete('invitations/{id}', [\App\Http\Controllers\Api\Hr\UsersController::class, 'deleteInvitation'])->name('invitations.delete');


        Route::post('logout', [\App\Http\Controllers\Api\DashboardController::class, 'logout']);

        Route::post('profile/', [\App\Http\Controllers\Api\DashboardController::class, 'profile'])->name('profile');
        Route::post('profile/update', [\App\Http\Controllers\Api\DashboardController::class, 'profileUpdate'])->name('profile-update');

        Route::group(['prefix' => 'notifications'], function () {
            Route::post('/', [\App\Http\Controllers\Api\DashboardController::class, 'notifications']);
            Route::post('read', [\App\Http\Controllers\Api\DashboardController::class, 'markAllAsRead']);
            Route::post('unread', [\App\Http\Controllers\Api\DashboardController::class, 'unreadNotifications']);
            Route::post('count', [\App\Http\Controllers\Api\DashboardController::class, 'count']);
            Route::post('read/{id}', [\App\Http\Controllers\Api\DashboardController::class, 'markAsRead']);

        });

        Route::group(['prefix' => 'users'], function () {
            Route::post('/', [\App\Http\Controllers\Api\Hr\UsersController::class, 'index']);
            Route::post('create', [\App\Http\Controllers\Api\Hr\UsersController::class, 'create']);
            Route::post('store', [\App\Http\Controllers\Api\Hr\UsersController::class, 'store']);
            Route::post('/toggle/{id}', [\App\Http\Controllers\Api\Hr\UsersController::class, 'toggle']);
            Route::post('/{id}', [\App\Http\Controllers\Api\Hr\UsersController::class, 'show']);
            Route::patch('/{id}', [\App\Http\Controllers\Api\Hr\UsersController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\Hr\UsersController::class, 'destroy']);
        });

        Route::group(['prefix' => 'roles'], function () {

            Route::post('/', [\App\Http\Controllers\Api\Hr\RolesController::class, 'index']);
            Route::post('create', [\App\Http\Controllers\Api\Hr\RolesController::class, 'create']);
            Route::post('store', [\App\Http\Controllers\Api\Hr\RolesController::class, 'store']);
            Route::post('permissions', [\App\Http\Controllers\Api\Hr\RolesController::class, 'permissions']);
            Route::post('/toggle/{id}', [\App\Http\Controllers\Api\Hr\RolesController::class, 'toggle']);
            Route::post('/{id}', [\App\Http\Controllers\Api\Hr\RolesController::class, 'show']);
            Route::patch('/{id}', [\App\Http\Controllers\Api\Hr\RolesController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\Hr\RolesController::class, 'destroy']);
        });

        ///*
        //|--------------------------------------------------------------------------
        //| Accounting API
        //|--------------------------------------------------------------------------
        //*/


        Route::group([
            'prefix' => 'accounting',
            'as' => 'accounting.',
        ], function () {

            Route::group([
                'prefix' => 'accounts',
                'as' => 'accounts.',
            ], function () {
                Route::post('/', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'index'])->name('index');
                Route::post('/list', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'list'])->name('list');
                Route::post('/categories', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'categories'])->name('categories');
                Route::post('/categories/{code}', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'category'])->name('category.show');
                Route::patch('/categories/{code}', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'updateCategory'])->name('category.update');


                Route::post('/create', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'create'])->name('create');
                Route::post('/store', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'store'])->name('store');
                Route::post('/{code}', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'show'])->name('show');
                Route::patch('/{code}', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'update'])->name('update');
            });

            Route::group([
                'prefix' => 'transactions',
                'as' => 'transactions.',
            ], function () {
                Route::post('/', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'index'])->name('index');
                Route::post('/list', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'list'])->name('list');
                Route::post('/create', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'create'])->name('create');
                Route::post('/store', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'store'])->name('store');
                Route::post('/store/type', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'storeType'])->name('storeType');
                Route::post('/edit/{id}', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'edit'])->name('edit');
                Route::post('/{code}', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'show'])->name('show');
            });

            Route::post('/tree', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'tree'])->name('tree');
            Route::post('/tree/duplicate', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'duplicate'])->name('duplicate');


            Route::post('/journal', [\App\Http\Controllers\Api\Accounting\WarehousesController::class, 'journal'])->name('journal');
            Route::post('/ledger', [\App\Http\Controllers\Api\Accounting\TransfersController::class, 'index'])->name('index');


            Route::group([
                'prefix' => 'entries',
                'as' => 'entries.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Accounting\EntriesController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Accounting\EntriesController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Accounting\EntriesController::class, 'edit'])->name('edit');
            });


            Route::get('/posting', [\App\Http\Controllers\Admin\Accounting\TransfersController::class, 'posting'])->name('posting');
            Route::get('/unposting', [\App\Http\Controllers\Admin\Accounting\TransfersController::class, 'unposting'])->name('unposting');

            Route::get('/reports', [\App\Http\Controllers\Admin\Accounting\ReportsController::class, 'index'])->name('reports');
        });


        /*
        |--------------------------------------------------------------------------
        | Purchases API
        |--------------------------------------------------------------------------
        */

        Route::group(['middleware' => ['auth:api']], function () {
            Route::post('bills', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'index']);
            Route::get('bills/create', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'create']);
            Route::post('bills/search-vendor', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'searchSupplier']);
            Route::post('bills/search-item', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'searchProduct']);

            Route::put('bills/store', [\App\Http\Controllers\Api\Purchases\BillsController::class, 'store']);

            Route::post('payments', [\App\Http\Controllers\Api\Purchases\PaymentsController::class, 'index']);
            Route::get('payments/create', [\App\Http\Controllers\Api\Purchases\PaymentsController::class, 'create']);
            Route::put('payments/store', [\App\Http\Controllers\Api\Purchases\PaymentsController::class, 'store']);

            Route::post('suppliers', [\App\Http\Controllers\Api\Purchases\SuppliersController::class, 'list']);
            Route::get('suppliers/create', [\App\Http\Controllers\Api\Purchases\SuppliersController::class, 'create']);
            Route::post('suppliers/get-states', [\App\Http\Controllers\Api\Purchases\SuppliersController::class, 'getStates']);
            Route::put('suppliers/{vendor?}', [\App\Http\Controllers\Api\Purchases\SuppliersController::class, 'store']);
            Route::get('suppliers/{id}', [\App\Http\Controllers\Api\Purchases\SuppliersController::class, 'show']);


            Route::post('contacts', [\App\Http\Controllers\Api\Purchases\SuppliersController::class, 'storeContact']);

        });


        Route::group([
            'prefix' => 'inventory',
            'as' => 'inventory.',
        ], function () {

            Route::group([
                'prefix' => 'products',
                'as' => 'products.',
            ], function () {
                Route::post('/', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'index'])->name('index');
                Route::post('/list', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'list'])->name('list');

                Route::post('/create', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'create'])->name('create');
                Route::post('/store', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'store'])->name('store');
                Route::post('/{id}', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'show'])->name('show');
                Route::patch('/{id}', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'update'])->name('update');
                Route::delete('/{product}', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'destroy'])->name('destroy');

            });

            Route::group([
                'prefix' => 'warehouses',
                'as' => 'warehouses.',
            ], function () {
                Route::post('/', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'index'])->name('index');
                Route::post('/list', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'list'])->name('list');
                Route::post('/create', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'create'])->name('create');
                Route::post('/store', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'store'])->name('store');
                Route::post('/{id}', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'show'])->name('show');
                Route::patch('{id}', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'update'])->name('update');
                Route::delete('/{warehouse}', [\App\Http\Controllers\Api\Inventory\WarehousesController::class, 'destroy'])->name('destroy');
            });



            Route::get('/transfer', [\App\Http\Controllers\Admin\Inventory\TransfersController::class, 'posting'])->name('posting');
            Route::get('/untransfer', [\App\Http\Controllers\Admin\Inventory\TransfersController::class, 'unposting'])->name('unposting');

            Route::get('/reports', [\App\Http\Controllers\Admin\Inventory\ReportsController::class, 'index'])->name('reports');
        });

        /*
        |--------------------------------------------------------------------------
        | System API
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'system'], function () {
            Route::post('currencies', [\App\Http\Controllers\Api\SystemController::class, 'currencies']);
            Route::post('currencies/{id}', [\App\Http\Controllers\Api\SystemController::class, 'getCurrency']);

            Route::post('countries', [\App\Http\Controllers\Api\SystemController::class, 'countries']);
            Route::post('countries/{id}', [\App\Http\Controllers\Api\SystemController::class, 'getCountry']);

            Route::post('states', [\App\Http\Controllers\Api\SystemController::class, 'states']);
            Route::post('states/{id}', [\App\Http\Controllers\Api\SystemController::class, 'getState']);

            Route::post('cities', [\App\Http\Controllers\Api\SystemController::class, 'cities']);
            Route::post('cities/{id}', [\App\Http\Controllers\Api\SystemController::class, 'getCity']);

            Route::post('taxes', [\App\Http\Controllers\Api\SystemController::class, 'taxes']);
            Route::post('taxes/{tax}', [\App\Http\Controllers\Api\SystemController::class, 'getTax']);


            Route::post('statuses/{id}', [\App\Http\Controllers\Api\SystemController::class, 'getStatus']);
            Route::post('addresses/{address}', [\App\Http\Controllers\Api\SystemController::class, 'getAddress']);
            Route::post('attachments/{attachment}', [\App\Http\Controllers\Api\SystemController::class, 'getAttachment']);
            Route::post('tags/{id}', [\App\Http\Controllers\Api\SystemController::class, 'getTag']);
            Route::post('contacts/{contact}', [\App\Http\Controllers\Api\SystemController::class, 'getContact']);
            Route::post('tickets/{ticket}', [\App\Http\Controllers\Api\SystemController::class, 'getTicket']);

        });

        /*
        |--------------------------------------------------------------------------
        | Settings API
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'settings'], function () {
            Route::post('/', [\App\Http\Controllers\Api\System\SettingController::class, 'index']);
            Route::post('{id}', [\App\Http\Controllers\Api\System\SettingController::class, 'show']);
            Route::patch('{id?}', [\App\Http\Controllers\Api\System\SettingController::class, 'update']);
            Route::delete('{id}', [\App\Http\Controllers\Api\System\SettingController::class, 'destroy']);
        });

    });

});
