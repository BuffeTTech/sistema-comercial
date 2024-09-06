<?php

namespace App\Policies;

use App\Models\Buffet;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SchedulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('list schedule');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list schedule');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('create schedule');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create schedule');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Schedule $schedule, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('update schedule');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update schedule');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Schedule $schedule, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('delete schedule');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete schedule');
        }

        return false;
    }

    
    public function change_status(User $user, Schedule $schedule, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('change schedule status');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('change schedule status');
        }

        return false;
    }
}
