<?php

namespace App\Policies;

use App\Models\PersonOneOnOneNote;
use App\Models\User;

class PersonOneOnOneNotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTechLead();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonOneOnOneNote $personOneOnOneNote): bool
    {
        return $user->isTechLead();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isTechLead();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonOneOnOneNote $personOneOnOneNote): bool
    {
        return $user->isTechLead();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonOneOnOneNote $personOneOnOneNote): bool
    {
        return $user->isTechLead();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PersonOneOnOneNote $personOneOnOneNote): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PersonOneOnOneNote $personOneOnOneNote): bool
    {
        return false;
    }
}
