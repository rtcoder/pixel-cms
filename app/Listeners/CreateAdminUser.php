<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Helpers\Helpers;
use App\Helpers\UserHelper;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        $admin = new User();
        $admin->fill([
            'email' => $event->client->email,
            'name' => 'Administrator',
        ]);
        $role = Role::where('is_admin', true)->first();
        $admin->client_id = $event->client->id;
        $admin->is_active = true;
        $admin->role_id = $role->id;

        $password = Helpers::generatePassword();
        $admin->password = Hash::make($password);
        $admin->save();


        Mail::send('emails.user_create', [
            'password' => $password
        ], function ($message) use ($admin) {
            $message->to($admin->email)
                ->subject('Pixel account');
            $message->from(env('MAIL_USERNAME'), 'Pixel');
        });
    }
}
