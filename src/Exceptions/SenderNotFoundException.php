<?php

namespace Synio\GmailServiceAccountMailDriver\Exceptions;

use Exception;
use Throwable;

class SenderNotFoundException extends Exception
{
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if ($message === null) {
            $message = 'Setting a sender address is required when using GMail API to send mails from a mailbox. You must set the sender address to a valid mailbox user.';
        }

        parent::__construct($message, $code, $previous);
    }
}
