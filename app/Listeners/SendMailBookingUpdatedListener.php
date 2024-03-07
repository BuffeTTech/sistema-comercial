<?php

namespace App\Listeners;

use App\Events\BookingUpdatedEvent;
use App\Notifications\BookingUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendMailBookingUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingUpdatedEvent $event): void
    {
        $user = auth()->user();
        Notification::send($user, new BookingUpdatedNotification($event->new_booking));

    }
}
