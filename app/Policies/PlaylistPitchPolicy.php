<?php

namespace App\Policies;

use App\Models\PlaylistPitch;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class PlaylistPitchPolicy
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
    public function view(User $user, PlaylistPitch $playlist_pitch): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $playlist_pitch->user_id;
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
    public function update(User $user, PlaylistPitch $playlist_pitch): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $playlist_pitch->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlaylistPitch $playlist_pitch): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $playlist_pitch->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlaylistPitch $playlist_pitch): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $playlist_pitch->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlaylistPitch $playlist_pitch): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $playlist_pitch->user_id;
    }
}
