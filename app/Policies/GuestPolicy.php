<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Buffet;
use App\Models\Guest;
use App\Models\User;

class GuestPolicy
{

    public function create(User $user, Booking $booking, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }
        
        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            if($booking->user_id == $user->id) {
                return true;
            }
            return $user->can('create guest');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create guest');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */

    public function change_status(User $user, Booking $booking, Guest $guest, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            if($booking->user_id == $user->id) {
                return true;
            }
            return $user->can('change guest status');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('change guest status');
        }

        return false;
    }
}
