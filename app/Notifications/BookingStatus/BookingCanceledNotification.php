<?php

namespace App\Notifications\BookingStatus;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCanceledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking
    )
    {
    }

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
        $url = url($this->booking->buffet->slug.'/booking/'.$this->booking->id.'/reschedule');
        return (new MailMessage)
                    ->subject('Festa Cancelada em '.$this->booking->buffet->trading_name)
                    ->greeting('Boa tarde, '.$notifiable->name.'!')
                    ->line('Sentimos muito que a sua experiencia no buffet '.$this->booking->buffet->trading_name.' não tenha sido muito boa!')
                    ->line('Caso tenha interesse em re-agendar sua festa em outro momento')
                    ->action('Clique aqui', $url)
                    ->line('Obrigado pela preferencia.');
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
