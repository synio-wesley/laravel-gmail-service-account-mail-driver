<?php

namespace Synio\GmailServiceAccountMailDriver\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Synio\GmailServiceAccountMailDriver\GmailServiceAccountMailDriver
 */
class GmailServiceAccountMailDriver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Synio\GmailServiceAccountMailDriver\GmailServiceAccountMailDriver::class;
    }
}
