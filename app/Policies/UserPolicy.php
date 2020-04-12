<?php

namespace App\Policies;

use App\Helpers\PermissionsHelper;
use App\Module;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE) || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE, 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE, 'create') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE, 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE, 'delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE, 'delete') || $user->id === $model->id;
    }
}
