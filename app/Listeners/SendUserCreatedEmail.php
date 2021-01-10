<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\App;

class SendUserCreatedEmail
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
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->getUser();
        $password=$event->getPassword();
        App::setLocale($user->client->locale);
        MailHelper::send(
            'emails.user_create',
            [
                'password' => $password,
                'email' => $user->email,
            ],
            $user->email,
            __('emails.account_created')
        );
    }
}
