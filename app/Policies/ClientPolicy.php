<?php

namespace App\Policies;

use App\Client;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
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

    public function update(User $user, Client $client)
    {
        return $user->client->is_superadmin || $user->role->is_admin && $client->id = $user->client_id;

    }

    public function create(?User $user)
    {
        return true;
    }

    public function view(User $user, Client $client)
    {
        return $user->client->is_superadmin || $user->role->is_admin && $client->id = $user->client_id;

    }

    public function delete(User $user)
    {
        return $user->client->is_superadmin;
    }
}
