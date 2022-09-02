<?php

namespace Synio\GmailServiceAccountMailDriver\Exceptions;

use Exception;
use Throwable;

class AuthConfigNotFoundException extends Exception
{
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if ($message === null) {
            $message = 'Auth config not found. Make sure that you have configured the `services.gmail_service_account.google_application_credentials` to point to a valid JSON file. By default it looks at the `GMAIL_SERVICE_ACCOUNT_GOOGLE_APPLICATION_CREDENTIALS` environment variable.';
        }

        parent::__construct($message, $code, $previous);
    }
}
