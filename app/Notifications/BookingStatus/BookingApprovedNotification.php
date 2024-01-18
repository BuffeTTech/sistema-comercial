<?php

namespace App\Notifications\BookingStatus;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
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
        $url = url($this->booking->buffet->slug.'/booking/'.$this->booking->id);
        return (new MailMessage)
                    ->subject('Festa Agendada em '.$this->booking->buffet->trading_name)
                    ->greeting('Boa tarde, '.$notifiable->name.'!')
                    ->line('Temos o prazer em confirmar a sua festa no buffet '.$this->booking->buffet->trading_name.'!')
                    ->line('A festa está agendada para '.date("Y-m-d H:i",strtotime(Carbon::parse($this->booking->party_day)->setHours($this->booking->schedule['start_time']))))
                    ->line('Para mais informações')
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
