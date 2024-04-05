<?php

namespace App\Policies;

use App\Models\Track;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class TrackPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view tracks') && $user->id === Auth::user()->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Track $track): bool
    {
        return $user->can('view tracks') && $user->id === $track->distribution->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create tracks') && $user->id === Auth::user()->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Track $track): bool
    {
        return $user->can('edit tracks') && $user->id === $track->distribution->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Track $track): bool
    {
        return $user->can('delete tracks') && $user->id === $track->distribution->user_id;
    }
}
