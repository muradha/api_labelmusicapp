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
        return $user->can('view bank withdraws');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BankWithdraw $bankWithdraw): bool
    {
        if ($user->can('view bank withdraws')) {
            if ($user->id === $bankWithdraw->user_id) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create bank withdraws');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BankWithdraw $bankWithdraw): bool
    {
        return $user->can('edit bank withdraws') && $user->id === $bankWithdraw->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BankWithdraw $bankWithdraw): bool
    {
        return $user->can('delete bank withdraws') && $user->id === $bankWithdraw->user_id;
    }
}
