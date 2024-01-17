<?php

namespace App\Notifications;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification implements ShouldQueue
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
        $url = url($this->booking->buffet->slug.'/booking/'.$this->booking->id);
        return (new MailMessage)
                    ->greeting('Boa tarde, '.$notifiable->name.'!')
                    ->line('Seu pedido para festa no buffet '.$this->booking->buffet->trading_name.' foi solicitado.')
                    ->line('Em breve retornaremos o status de sua solicitação!')
                    ->action('Ver solicitação', $url)
                    ->line('Dados da reserva:')
                    ->line('Dia da festa: '.date("Y-m-d H:i",strtotime(Carbon::parse($this->booking->party_day)->setHours($this->booking->schedule['start_time']))))
                    ->line('Pacote de comida escolhido: '.$this->booking->food->name_food)
                    ->line('Pacote de decorações escolhido: '.$this->booking->food->name_food)
                    ->line('Preço final: R$'.$this->booking->price_food + $this->booking->price_decoration + $this->booking->price_schedule)
                    ->line('Thank you for using our application!');
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
