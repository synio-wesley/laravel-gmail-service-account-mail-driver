<?php

namespace Synio\GmailServiceAccountMailDriver;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
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

        if ($this->apiClient === null) {
            $this->apiClient = new Client();
            $this->apiClient->setAuthConfig(config('services.gmail_service_account.google_application_credentials'));
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
        // TODO: test doSend method somehow (but mock Client, Gmail)
        // TODO: README

        // TODO: publish package?
        // TODO: use package
        // TODO: replace Mail app password with list of allowed senders
        // TODO: add From selector for step
        // TODO: add CC, BCC fields for step
        // TODO: ability to override From, CC, BCC before completing step

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
