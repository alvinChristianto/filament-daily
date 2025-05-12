<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AcCustomer;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcCustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_ac::customer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcCustomer $acCustomer): bool
    {
        return $user->can('view_ac::customer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ac::customer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcCustomer $acCustomer): bool
    {
        return $user->can('update_ac::customer');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcCustomer $acCustomer): bool
    {
        return $user->can('delete_ac::customer');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_ac::customer');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AcCustomer $acCustomer): bool
    {
        return $user->can('force_delete_ac::customer');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_ac::customer');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AcCustomer $acCustomer): bool
    {
        return $user->can('restore_ac::customer');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_ac::customer');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AcCustomer $acCustomer): bool
    {
        return $user->can('replicate_ac::customer');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_ac::customer');
    }
}
