<?php

namespace App\Providers;

use App\Events\BookingCreatedEvent;
use App\Events\ChangeBookingStatusEvent;
use App\Listeners\SendMailBookingCreatedListener;
use App\Listeners\SendMailBookingStatusListener;
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

        BookingCreatedEvent::class => [
            SendMailBookingCreatedListener::class
        ],
        ChangeBookingStatusEvent::class => [
            SendMailBookingStatusListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
