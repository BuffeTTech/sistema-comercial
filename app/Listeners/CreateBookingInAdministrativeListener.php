<?php

namespace App\Listeners;

use App\Events\BookingCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class CreateBookingInAdministrativeListener
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
    public function handle(BookingCreatedEvent $event): void
    {
        $booking = $event->booking;
        $booking['schedule'] = $event->booking->schedule;
        $response = Http::acceptJson()->post(config('app.administrative_url').'/api'.'/'.$event->booking->buffet->slug.'/'.'booking/', ['booking'=>$booking]);
    }
}
