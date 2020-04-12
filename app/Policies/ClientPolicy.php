<?php

namespace App\Policies;

use App\Client;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
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
        return $user->client->is_superadmin;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function view(User $user, Client $client)
    {
        return $user->client->is_superadmin || $client->id != $user->clientId;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->client->is_superadmin;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function update(User $user, Client $client)
    {
        return $user->client->is_superadmin || $client->id != $user->clientId;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function delete(User $user, Client $client)
    {
        return $user->client->is_superadmin;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function restore(User $user, Client $client)
    {
        return $user->client->is_superadmin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Client $client
     * @return mixed
     */
    public function forceDelete(User $user, Client $client)
    {
        return $user->client->is_superadmin;
    }
}
