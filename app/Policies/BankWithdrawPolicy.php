<?php

namespace App\Policies;

use App\Models\BankWithdraw;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class BankWithdrawPolicy
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
    public function view(User $user, BankWithdraw $bankWithdraw): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $bankWithdraw->user_id;
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
    public function update(User $user, BankWithdraw $bankWithdraw): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $bankWithdraw->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BankWithdraw $bankWithdraw): bool
    {
        return $user->hasAnyRole('admin') ?: $user->id === $bankWithdraw->user_id;
    }
}
