<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Auth\Access\Response;
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
        return $user->hasAnyRole('admin') ?: $user->id === $artist->user_id;
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
    public function update(User $user, Artist $artist): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $artist->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Artist $artist): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $artist->user_id;
    }
}
