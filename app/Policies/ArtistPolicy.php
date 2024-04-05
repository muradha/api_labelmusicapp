<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArtistPolicy
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
    public function view(User $user, Artist $artist): bool
    {
        if ($user->can('view artists')) {
            if ($user->id === $artist->user_id) {
                return true;
            }
            if (!empty($user->curentTeam) && $user->currentTeam->owner->id === $artist->user_id) {
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
        return $user->can('create artists');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Artist $artist): bool
    {
        if($user->can('edit artists')){
            if ($user->id === $artist->user_id) {
                return true;
            }
            if (!empty($user->curentTeam) && $user->currentTeam->owner->id === $artist->user_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Artist $artist): bool
    {
        if($user->can('delete artists')){
            if ($user->id === $artist->user_id) {
                return true;
            }
            if (!empty($user->curentTeam) && $user->currentTeam->owner->id === $artist->user_id) {
                return true;
            }
        }

        return false;
    }
}
