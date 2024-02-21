<?php

namespace App\Notifications;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url($this->booking->buffet->slug.'/booking/'.$this->booking->hashed_id);
        return (new MailMessage)
                    ->greeting('Boa tarde, '.$notifiable->name.'!')
                    ->line('Seu pedido para festa no buffet '.$this->booking->buffet->trading_name.' foi alterado.')
                    ->line('Clique no botão abaixo para ver as alterações em sua reserva.')
                    ->action('Ver reserva', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
