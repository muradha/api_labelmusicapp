<?php

namespace App\Policies;

use App\Models\Analytic;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnalyticPolicy
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const VIEW = 'view';
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view analytics');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Analytic $analytic): bool
    {
        if($user->can('view analytics') && $user->hasAnyRole('admin', 'super-admin', 'operator')){
            return true;
        }
        if($user->can('view analytics') && $user->id === $analytic->user_id){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create analytics');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Analytic $analytic): bool
    {
        if($user->can('edit analytics') && $user->hasAnyRole('admin', 'super-admin', 'operator')){
            return true;
        }
        if($user->can('edit analytics') && $user->id === $analytic->user_id){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Analytic $analytic): bool
    {
        if($user->can('delete analytics') && $user->hasAnyRole('admin', 'super-admin', 'operator')){
            return true;
        }
        if($user->can('delete analytics') && $user->id === $analytic->user_id){
            return true;
        }

        return false;
    }

}
