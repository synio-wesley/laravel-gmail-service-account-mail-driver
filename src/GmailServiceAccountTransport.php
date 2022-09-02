<?php

namespace Synio\GmailServiceAccountMailDriver;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Synio\GmailServiceAccountMailDriver\Exceptions\AuthConfigNotFoundException;
use Synio\GmailServiceAccountMailDriver\Exceptions\InvalidGrantException;
use Synio\GmailServiceAccountMailDriver\Exceptions\SenderNotFoundException;

class GmailServiceAccountTransport extends AbstractTransport
{
    /**
     * Create a new transport instance.
     *
     * @return void
     */
    public function __construct(private ?Client $apiClient = null, private ?Gmail $gmailService = null)
    {
        parent::__construct();

        $path = config('services.gmail_service_account.google_application_credentials', env('GMAIL_SERVICE_ACCOUNT_GOOGLE_APPLICATION_CREDENTIALS'));
        if (!is_file($path)) {
            // Try relative path if absolute path cannot be found
            $path = base_path($path);
        }
        if (!is_file($path)) {
            throw new AuthConfigNotFoundException();
        }

        if ($this->apiClient === null) {
            $this->apiClient = new Client();
            $this->apiClient->setAuthConfig($path);
            $this->apiClient->setApplicationName(config('app.name'));
            $this->apiClient->setScopes(['https://www.googleapis.com/auth/gmail.send']);
        }

        if ($this->gmailService === null) {
            $this->gmailService = new Gmail($this->apiClient);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        if (!$email->getSender() && empty($email->getFrom())) {
            throw new SenderNotFoundException();
        }

        $sender = $email->getSender() ?? $email->getFrom()[0];
        $userToImpersonate = $sender->getAddress();

        $this->apiClient->setSubject($userToImpersonate);

        $gmailMessage = new Message();
        $gmailMessage->setRaw(rtrim(strtr(base64_encode($message->toString()), '+/', '-_'), '='));

        try {
            $this->gmailService->users_messages->send($userToImpersonate, $gmailMessage);
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() === 400) {
                $decoded = json_decode($e->getMessage());
                if ($decoded && data_get($decoded, 'error') === 'invalid_grant') {
                    throw new InvalidGrantException();
                }
            }
            throw $e;
        }
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'gmail-service-account';
    }
}
