<?php

namespace App\Policies;

use App\Models\Buffet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BuffetPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Buffet $buffet): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Buffet $buffet): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Buffet $buffet): bool
    {
        //
    }
}
