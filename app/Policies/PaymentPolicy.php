<?php

namespace App\Policies;

use App\Models\User;

class PaymentPolicy
{
    // /**
    //  * Create a new policy instance.
    //  */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Employee', 'Doctor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        // Users can view their own payments
        if ($user->id === $payment->patient_id) {
            return true;
        }

        // Admin and staff can view all payments
        return $user->hasAnyRole(['Admin', 'Employee', 'Doctor']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create payments
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        // Only admin and staff can update payments
        return $user->hasAnyRole(['Admin', 'Employee']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payment $payment): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->hasRole('Admin');
    }
}
