<?php

namespace App\Providers;

use App\Events\BookingCreatedEvent;
use App\Events\BookingUpdatedEvent;
use App\Events\ChangeBookingStatusEvent;
use App\Events\EditBuffetEvent;
use App\Listeners\ChangeBookingStatusInAdministrativeListener;
use App\Listeners\CreateBookingInAdministrativeListener;
use App\Listeners\EditBookingInAdministrativeListener;
use App\Listeners\EditBuffetInAdministrativeListener;
use App\Listeners\SendMailBookingCreatedListener;
use App\Listeners\SendMailBookingStatusListener;
use App\Listeners\SendMailBookingUpdatedListener;
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
            SendMailBookingCreatedListener::class,
            CreateBookingInAdministrativeListener::class
        ],
        ChangeBookingStatusEvent::class => [
            SendMailBookingStatusListener::class,
            ChangeBookingStatusInAdministrativeListener::class
        ],
        EditBuffetEvent::class => [
            EditBuffetInAdministrativeListener::class
        ],
        BookingUpdatedEvent::class => [
            EditBookingInAdministrativeListener::class,
            SendMailBookingUpdatedListener::class
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
