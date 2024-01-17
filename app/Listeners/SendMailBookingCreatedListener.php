<?php

namespace App\Listeners;

use App\Events\BookingCreatedEvent;
use App\Notifications\BookingCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendMailBookingCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreatedEvent $event): void
    {
        $user = auth()->user();
        Notification::send($user, new BookingCreatedNotification($event->booking));

    }
}
