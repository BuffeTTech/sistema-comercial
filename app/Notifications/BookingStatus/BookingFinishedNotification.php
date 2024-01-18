<?php

namespace App\Notifications\BookingStatus;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingFinishedNotification extends Notification implements ShouldQueue
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
        $url = url($this->booking->buffet->slug.'/dashboard');
        return (new MailMessage)
                    ->greeting('Boa tarde, '.$notifiable->name.'!')
                    ->line('Agradecemos e o parabenizamos pela sua festa no buffet '.$this->booking->buffet->trading_name.'!')
                    ->line('Para finalizar nosso atendimento, pedimos que responda a nossa pesquisa de satisfação através do link apresentado no botão abaixo')
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
