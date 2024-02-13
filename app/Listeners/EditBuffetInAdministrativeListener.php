<?php

namespace App\Listeners;

use App\Events\EditBuffetEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class EditBuffetInAdministrativeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    public function handle(EditBuffetEvent $event): void
    {
        $buffet = $event->buffet;
        $buffet['address'] = $event->buffet->buffet_address ?? null;
        $buffet['phone1'] = $event->buffet->buffet_phone1 ?? null;
        $buffet['phone2'] = $event->buffet->buffet_phone2 ?? null;

        try {

            $response = Http::acceptJson()->put(config('app.administrative_url').'/api/buffet/'.$event->old_slug, ['buffet'=>$event->buffet]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $response = $e->response;

            $statusCode = $response->status();
            $errorMessage = $response->json()['error'] ?? 'Erro desconhecido';
        }
    }

}
