<?php

namespace App\Policies;

use App\Models\PaypalWithdraw;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PaypalWithdrawPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view paypal withdraws');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaypalWithdraw $paypalWithdraw): bool
    {
        return $user->can('view paypal withdraws') && $user->id === $paypalWithdraw->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create paypal withdraws');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaypalWithdraw $paypalWithdraw): bool
    {
        return $user->can('edit paypal withdraws') && $user->id === $paypalWithdraw->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaypalWithdraw $paypalWithdraw): bool
    {
        return $user->can('delete paypal withdraws') && $user->id === $paypalWithdraw->user_id;
    }
}
