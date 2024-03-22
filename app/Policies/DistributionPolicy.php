<?php

namespace App\Policies;

use App\Models\Distribution;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DistributionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->id === Auth::user()->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Distribution $distribution): bool
    {
        return $user->hasAnyRole('admin') ?: ($user->id === $distribution->user_id) || ($user->currentTeam->owner->id === $distribution->user_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->id === Auth::user()->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Distribution $distribution): bool
    {
        return $user->hasAnyRole('admin') ?: ($user->id === $distribution->user_id) || ($user->currentTeam->owner->id === $distribution->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Distribution $distribution): bool
    {
        return $user->hasAnyRole('admin') ?: ($user->id === $distribution->user_id) || ($user->currentTeam->owner->id === $distribution->user_id);
    }
}
