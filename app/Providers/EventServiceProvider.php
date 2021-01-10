<?php

namespace App\Providers;

use App\Events\ClientCreated;
use App\Events\UserCreated;
use App\Listeners\CreateAdminUser;
use App\Listeners\SendUserCreatedEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ClientCreated::class => [
            CreateAdminUser::class
        ],
        UserCreated::class => [
            SendUserCreatedEmail::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
