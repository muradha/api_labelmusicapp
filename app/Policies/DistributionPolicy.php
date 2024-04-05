<?php

namespace App\Policies;

use App\Models\Distribution;
use App\Models\User;

class DistributionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view distributions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Distribution $distribution): bool
    {
        if ($user->can('view distributions')) {
            if ($user->id === $distribution->user_id) return true;
            if (!empty($user->curentTeam) && $user->currentTeam->owner->id === $distribution->user_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create distributions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Distribution $distribution): bool
    {
        if($user->can('edit distributions')){
            if ($user->id === $distribution->user_id) {
                return true;
            }
            if (!empty($user->curentTeam) && $user->currentTeam->owner->id === $distribution->user_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Distribution $distribution): bool
    {
        if($user->can('delete distributions')){
            if ($user->id === $distribution->user_id) {
                return true;
            }
            if (!empty($user->curentTeam) && $user->currentTeam->owner->id === $distribution->user_id) {
                return true;
            }
        }
      
        return false;
    }
}
