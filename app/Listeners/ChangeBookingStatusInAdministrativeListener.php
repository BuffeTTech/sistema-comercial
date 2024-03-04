<?php

namespace App\Listeners;

use App\Events\ChangeBookingStatusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class ChangeBookingStatusInAdministrativeListener
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
    public function handle(ChangeBookingStatusEvent $event): void
    {
        $response = Http::acceptJson()->put(config('app.administrative_url').'/api'.'/'.$event->booking->buffet->slug.'/'.'booking/status', ['booking'=>$event->booking]);
        dd($response->body());
    }
}
