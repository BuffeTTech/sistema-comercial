<?php

namespace App\Policies;

use App\Models\Buffet;
use App\Models\SatisfactionQuestion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SurveyQuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('list all survey question');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list all survey question');
        }

        return false;
    }

    public function viewAnyBuffet(User $user, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('list all buffet survey question');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list all buffet survey question');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SatisfactionQuestion $satisfactionQuestion, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }

        
        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $satisfactionQuestion->buffet_id) {
            return $user->can('show survey question');
        }
        
        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('show survey question');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('create survey question');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create survey question');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SatisfactionQuestion $satisfactionQuestion, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('update survey question');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update survey question');
        }

        return $user->can('update survey question');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SatisfactionQuestion $satisfactionQuestion, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }
        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('delete survey question');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete survey question');
        }

        return $user->can('delete survey question');
    }

    public function answer(User $user, SatisfactionQuestion $satisfactionQuestion, Buffet $buffet): bool
    {
        if ($user === null) {
            return false;
        }
        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('answer survey question');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('answer survey question');
        }

        return $user->can('answer survey question');
    }
}
