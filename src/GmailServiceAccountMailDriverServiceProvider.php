<?php

namespace Synio\GmailServiceAccountMailDriver;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class GmailServiceAccountMailDriverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Mail::extend('gmail-service-account', function () {
            return new GmailServiceAccountTransport();
        });
    }
}
