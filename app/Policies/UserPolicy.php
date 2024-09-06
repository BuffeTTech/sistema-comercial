<?php

namespace App\Policies;

use App\Models\Buffet;
use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

        /**
     * Determine whether the user can view any models.
     */
    public function viewAnyEmployee(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('list employee');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list employee');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewEmployee(User $user, User $employee, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('view employee');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('view employee');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function createEmployee(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('create employee');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create employee');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateEmployee(User $user, User $employee, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('update employee');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update employee');
        }

        return false;
    }

    public function changeUserRole(User $user, User $employee, Buffet $buffet) {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('change buffet user role');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('change buffet user role');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteEmployee(User $user, User $employee, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('delete employee');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete employee');
        }

        return false;
    }

    public function viewAnyUser(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('list buffet user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list buffet user');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewUser(User $user, User $employee, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('view buffet user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('view buffet user');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function createUser(User $user, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('create buffet user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create buffet user');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateUser(User $user, User $employee, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('update buffet user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update buffet user');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteUser(User $user, User $employee, Buffet $buffet): bool
    {
        if($user == null) {
            return false;
        }

        // Verifica se o usuário é cadastrado no buffet
        if($user->buffet_id == $buffet->id) {
            return $user->can('delete buffet user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete buffet user');
        }

        return false;
    }
}
