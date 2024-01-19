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

//Route::get('/download', [\App\Http\Controllers\Api\Inventory\ProductsController::class, 'download'])->name('download');


Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
    Route::get('register', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'register'])->name('register');

    Route::group(['middleware' => ['auth', 'active']], function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
        Route::get('/profile', [\App\Http\Controllers\Admin\DashboardController::class, 'profile'])->name('profile');
        Route::get('/notifications', [\App\Http\Controllers\Admin\DashboardController::class, 'notifications'])->name('notifications');
        Route::get('/reports', [\App\Http\Controllers\Admin\DashboardController::class, 'reports'])->name('reports.index');


        Route::group([
            'prefix' => 'users',
            'as' => 'users.',
        ], function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
            Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        });

        Route::group([
            'prefix' => 'roles',
            'as' => 'roles.',
        ], function () {
            Route::get('/index', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
            Route::get('/edit/{role_id}', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
        });

        Route::group([
            'prefix' => 'clients',
            'as' => 'clients.',
        ], function () {
            Route::get('/', [\App\Http\Controllers\Admin\ClientController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ClientController::class, 'create'])->name('create');
            Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\ClientController::class, 'edit'])->name('edit');
            Route::get('/actions', [\App\Http\Controllers\Admin\Crm\ActionsController::class, 'index'])->name('actions.index');
        });
        Route::group([
            'prefix' => 'accounting',
            'as' => 'accounting.',
        ], function () {

            Route::group([
                'prefix' => 'accounts',
                'as' => 'accounts.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Accounting\AccountsController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Accounting\AccountsController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Accounting\AccountsController::class, 'edit'])->name('edit');
                Route::get('/charts', [\App\Http\Controllers\Admin\Accounting\AccountsController::class, 'charts'])->name('charts');
                Route::get('/{code}', [\App\Http\Controllers\Admin\Accounting\AccountsController::class, 'show'])->name('show');
            });
            Route::get('/category/{slug}', [\App\Http\Controllers\Admin\Accounting\AccountsController::class, 'category'])->name('category.show');

            Route::get('/cash-in', [\App\Http\Controllers\Admin\Accounting\TransactionsController::class, 'cashIn'])->name('cash-in');
            Route::get('/cash-out', [\App\Http\Controllers\Admin\Accounting\TransactionsController::class, 'cashOut'])->name('cash-out');

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

        Route::group([
            'prefix' => 'purchases',
            'as' => 'purchases.',
        ], function () {

            Route::group([
                'prefix' => 'bills',
                'as' => 'bills.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Purchases\BillsController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Purchases\BillsController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Purchases\BillsController::class, 'edit'])->name('edit');
            });
            Route::group([
                'prefix' => 'suppliers',
                'as' => 'suppliers.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Purchases\SupplierController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Purchases\SupplierController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Purchases\SupplierController::class, 'edit'])->name('edit');
            });
            Route::group([
                'prefix' => 'payments',
                'as' => 'payments.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Purchases\PaymentController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Purchases\PaymentController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Purchases\PaymentController::class, 'edit'])->name('edit');
            });
            Route::group([
                'prefix' => 'returns',
                'as' => 'returns.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Purchases\ReturnsController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Purchases\ReturnsController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Purchases\ReturnsController::class, 'edit'])->name('edit');
            });

            Route::get('/reports', [\App\Http\Controllers\Admin\Purchases\ReportsController::class, 'create'])->name('reports');

        });

        Route::group([
            'prefix' => 'sales',
            'as' => 'sales.',
        ], function () {

            Route::group([
                'prefix' => 'invoices',
                'as' => 'invoices.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Sales\InvoicesController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Sales\InvoicesController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Sales\InvoicesController::class, 'edit'])->name('edit');
            });
            Route::group([
                'prefix' => 'revenues',
                'as' => 'revenues.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Sales\RevenuesController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Sales\RevenuesController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Sales\RevenuesController::class, 'edit'])->name('edit');
            });
            Route::group([
                'prefix' => 'returns',
                'as' => 'returns.',
            ], function () {
                Route::get('/', [\App\Http\Controllers\Admin\Sales\ReturnsController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\Sales\ReturnsController::class, 'create'])->name('create');
                Route::get('/edit/{user_id}', [\App\Http\Controllers\Admin\Sales\ReturnsController::class, 'edit'])->name('edit');
            });

            Route::get('/reports', [\App\Http\Controllers\Admin\Purchases\ReportsController::class, 'create'])->name('reports');

        });

        Route::get('/help-center', [\App\Http\Controllers\Admin\DashboardController::class, 'help'])->name('help.index');

        Route::group([
            'prefix' => 'setting',
            'as' => 'setting.',
        ], function () {
            Route::get('/app', [\App\Http\Controllers\Admin\Setting\AppController::class, 'index'])->name('app.index');
            Route::get('/business', [\App\Http\Controllers\Admin\Setting\BusinessController::class, 'index'])->name('business.index');
            Route::get('/invitations', [\App\Http\Controllers\Admin\Setting\InvitationsController::class, 'index'])->name('invitations.index');
        });

    });
});
