<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Record successful logins with the request IP and browser.
     */
    public function handle(Login $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'browser' => request()->userAgent(),
            ])
            ->log('ورود به سیستم');
    }
}
