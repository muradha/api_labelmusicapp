<?php

namespace App\Policies;

use App\Models\User;
use App\Models\YoutubeOac;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class YoutubeOacPolicy
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
    public function view(User $user, YoutubeOac $youtube_oac): bool
    {
        return $user->hasAnyRole('admin', 'super-admin') ?: $user->id === $youtube_oac->user_id;
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
    public function update(User $user, YoutubeOac $youtube_oac): bool
    {
        return $user->hasAnyRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, YoutubeOac $youtube_oac): bool
    {
        return $user->hasAnyRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, YoutubeOac $youtube_oac): bool
    {
        return $user->hasAnyRole('admin');
    }
}
