<?php

namespace App\Notifications\BookingStatus;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRejectedNotification extends Notification implements ShouldQueue
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
                    ->subject('Festa Negada em '.$this->booking->buffet->trading_name)
                    ->greeting('Boa tarde, '.$notifiable->name.'!')
                    ->line('Infelizmente, sua festa no buffet '.$this->booking->buffet->trading_name.' no dia '.date("Y-m-d",strtotime(Carbon::parse($this->booking->party_day))).' foi negada')
                    // ->line('Caso tenha interesse em re-agendar sua festa em outro momento')
                    // ->action('Clique aqui', $url)
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
