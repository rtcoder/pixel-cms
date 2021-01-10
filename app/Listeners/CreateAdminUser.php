<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Events\UserCreated;
use App\Helpers\Helpers;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ClientCreated $event
     * @return void
     */
    public function handle(ClientCreated $event)
    {
        $client = $event->getClient();
        $admin = new User();
        $admin->fill([
            'email' => $client->email,
            'name' => 'Administrator',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Administrator',
            'type' => Role::ADMIN,
        ]);
        $role->client_id = $client->id;
        $role->save();

        $admin->client_id = $client->id;
        $admin->is_active = true;
        $admin->role_id = $role->id;

        $password = Helpers::generatePassword();
        $admin->password = Hash::make($password);
        $admin->save();

        event(new UserCreated($admin, $password));
    }
}
