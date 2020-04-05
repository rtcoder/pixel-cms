<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $auth)
    {
        return $auth->role->is_admin;
    }

    public function update(User $auth, User $user)
    {
        return $auth->client_id === $user->client_id && $auth->role->is_admin || $auth->id === $user->id;
    }

    public function view(User $auth, User $user)
    {
        return $auth->client_id === $user->client_id && $auth->role->is_admin || $auth->id === $user->id;
    }

    public function delete(User $auth, User $user)
    {
        return $auth->client_id === $user->client_id && $auth->role->is_admin
            && (!$user->role->is_admin || User::where('client_id', $auth->client_id)->whereHas('role', function ($q) {
                    $q->where('is_admin', true);
                })->count() > 1);
    }
}
