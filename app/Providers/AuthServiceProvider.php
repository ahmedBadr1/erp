<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\System\Address;
use App\Models\System\Attachment;
use App\Models\System\Contact;
use App\Policies\System\AddressPolicy;
use App\Policies\System\AttachmentPolicy;
use App\Policies\System\ContactPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Address::class => AddressPolicy::class,
        Contact::class => ContactPolicy::class,
        Attachment::class => AttachmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->id === 1 ? true : null;
        });

//        Passport::tokensExpireIn(now()->addMinutes(30));
//        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMinutes(30));

    }
}
