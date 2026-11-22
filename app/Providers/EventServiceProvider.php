<?php

namespace App\Providers;

use App\Listeners\LogSuccessfulLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string, list<class-string>> */
    protected $listen = [
        Login::class => [
            LogSuccessfulLogin::class,
        ],
    ];
}
