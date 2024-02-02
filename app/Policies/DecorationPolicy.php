<?php

namespace App\Policies;

use App\Models\Buffet;
use App\Models\Decoration;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DecorationPolicy
{
    public function viewAny(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('list decoration');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list decoration');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Decoration $decoration, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('show decoration');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('show decoration');
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
            return $user->can('create decoration');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create decoration');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Decoration $decoration, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('update decoration');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update decoration');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Decoration $decoration, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('delete decoration');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete decoration');
        }

        return false;
    }

    public function change_status(User $user, Decoration $decoration, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('change decoration status');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('change decoration status');
        }

        return false;
    }
}
