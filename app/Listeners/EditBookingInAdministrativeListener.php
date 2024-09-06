<?php

namespace App\Listeners;

use App\Events\BookingUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class EditBookingInAdministrativeListener
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
        $new_booking = $event->new_booking;
        $new_booking['schedule'] = $event->new_booking->schedule;

        $original_booking = $event->original_booking;
        $new_booking['schedule'] = $event->original_booking->schedule;

        $response = Http::acceptJson()->put(config('app.administrative_url').'/api'.'/'.$event->original_booking->buffet->slug.'/'.'booking/', ['new_booking'=>$new_booking, 'original_booking'=>$original_booking]);
    }
}
