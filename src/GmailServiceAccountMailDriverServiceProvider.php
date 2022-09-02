<?php

namespace Synio\GmailServiceAccountMailDriver;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class GmailServiceAccountMailDriverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Mail::extend('gmail-service-account', function (array $config = []) {
            return new GmailServiceAccountTransport(
                Arr::get($config, 'api_client'),
                Arr::get($config, 'gmail_service')
            );
        });
    }
}
