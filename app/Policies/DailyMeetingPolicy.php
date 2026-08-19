<?php

namespace App\Policies;

use App\Models\DailyMeeting;
use App\Models\User;

/**
 * Daily meetings are append-only history: update/delete/restore/forceDelete are always denied here
 * (in addition to those verbs simply not being routed) so the "never edit history" intent lives in
 * code, not just in routes/api.php.
 */
class DailyMeetingPolicy
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
    public function view(User $user, DailyMeeting $dailyMeeting): bool
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
    public function update(User $user, DailyMeeting $dailyMeeting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DailyMeeting $dailyMeeting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DailyMeeting $dailyMeeting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DailyMeeting $dailyMeeting): bool
    {
        return false;
    }
}
