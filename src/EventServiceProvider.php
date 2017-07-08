<?php

namespace DeveoDK\LaravelApiAuthenticator;

use DeveoDK\LaravelApiAuthenticator\events\AuthenticateAttempt;
use DeveoDK\LaravelApiAuthenticator\Listeners\AuthenticateAttemptListener;
use DeveoDK\LaravelApiAuthenticator\Listeners\AuthenticateListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use DeveoDK\LaravelApiAuthenticator\events\UserWasAuthenticated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
        UserWasAuthenticated::class => [
            AuthenticateListener::class,
        ],
        AuthenticateAttempt::class => [
            AuthenticateAttemptListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
