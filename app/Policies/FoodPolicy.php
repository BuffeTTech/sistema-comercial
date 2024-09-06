<?php

namespace App\Policies;

use App\Models\Buffet;
use App\Models\Food;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FoodPolicy
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
            return $user->can('list food');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list food');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Food $food, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('view food');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('view food');
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
            return $user->can('create food');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create food');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Food $food, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('update food');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update food');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Food $food, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }
        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('delete food');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete food');
        }

        return false;
    }

    public function change_status(User $user, Food $food, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('change food status');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('change food status');
        }

        return false;
    }
}
