<?php

namespace App\Providers;

use App\Models\Inventory\OtherParty;
use App\Models\Inventory\Warehouse;
use App\Models\System\Attachment;
use App\Models\User;
use App\Observers\Inventory\OtherPartyObserver;
use App\Observers\Inventory\WarehouseObserver;
use App\Observers\System\AttachmentObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $observers = [
        User::class => [UserObserver::class],
        Warehouse::class => [WarehouseObserver::class],
        OtherParty::class => [OtherPartyObserver::class],

        Attachment::class => [AttachmentObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
