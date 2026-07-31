<?php

namespace App\Policies;

use App\Models\DailyMeetingEntry;
use App\Models\User;

/**
 * Entries are read-only via the API — they only ever come into existence inside
 * DailyMeetingStoreService's transaction, never through a direct create/update/delete request.
 */
class DailyMeetingEntryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DailyMeetingEntry $dailyMeetingEntry): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DailyMeetingEntry $dailyMeetingEntry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DailyMeetingEntry $dailyMeetingEntry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DailyMeetingEntry $dailyMeetingEntry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DailyMeetingEntry $dailyMeetingEntry): bool
    {
        return false;
    }
}
