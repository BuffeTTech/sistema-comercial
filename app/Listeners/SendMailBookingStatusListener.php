<?php

namespace App\Listeners;

use App\Enums\BookingStatus;
use App\Events\ChangeBookingStatusEvent;
use App\Notifications\BookingStatus\BookingApprovedNotification;
use App\Notifications\BookingStatus\BookingCanceledNotification;
use App\Notifications\BookingStatus\BookingClosedNotification;
use App\Notifications\BookingStatus\BookingFinishedNotification;
use App\Notifications\BookingStatus\BookingRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendMailBookingStatusListener
{
    public function __construct()
    {
    }

    public function handle(ChangeBookingStatusEvent $event): void
    {
        $user = $event->booking->user;
        switch($event->booking->status) {
            case BookingStatus::PENDENT->name:
                break;
            case BookingStatus::APPROVED->name:
                Notification::send($user, new BookingApprovedNotification($event->booking));
                break;
            case BookingStatus::REJECTED->name:
                Notification::send($user, new BookingRejectedNotification($event->booking));
                break;
            case BookingStatus::CANCELED->name:
                // Notification::send($user, new BookingCanceledNotification($event->booking));
                break;
            case BookingStatus::FINISHED->name:
                Notification::send($user, new BookingFinishedNotification($event->booking));
                break;
            case BookingStatus::CLOSED->name:
                Notification::send($user, new BookingClosedNotification($event->booking));
                break;
        }
    }
}
