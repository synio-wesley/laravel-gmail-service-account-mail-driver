<?php

namespace Synio\GmailServiceAccountMailDriver\Exceptions;

use Exception;
use Throwable;

class InvalidGrantException extends Exception
{
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if ($message === null) {
            $message = 'GMail API returned "invalid_grant". This probably means that your sender address is not a valid Google Workspace mailbox user. Or maybe you are using the wrong JSON credentials file.';
        }

        parent::__construct($message, $code, $previous);
    }
}
