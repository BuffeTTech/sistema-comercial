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
            return $user->can('show employee');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('show employee');
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
            return $user->can('change user role');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('change user role');
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
            return $user->can('list user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('list user');
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
            return $user->can('show user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('show user');
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
            return $user->can('create user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('create user');
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
            return $user->can('update user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('update user');
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
            return $user->can('delete user');
        }

        // Verifica se usuário é o dono do buffet
        if($user->id == $buffet->owner_id) {
            return $user->can('delete user');
        }

        return false;
    }
}
