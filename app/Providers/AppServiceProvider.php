<?php

namespace App\Providers;

use App\Http\Resources\Dashboard\NotificationCollection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        Relation::morphMap([
            'users' => User::class,
        ]);

        $localeDirs = config('languages.localeDirs');
        $langs = config('languages.langs');
        View::share('localeDirs', $localeDirs);
        View::share('langs', $langs);
//        JsonResource::withoutWrapping();

    }
}
