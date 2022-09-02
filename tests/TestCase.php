<?php

namespace Synio\GmailServiceAccountMailDriver\Tests;

use Google\Client;
use Google\Service\Gmail;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use Synio\GmailServiceAccountMailDriver\GmailServiceAccountMailDriverServiceProvider;

class TestCase extends Orchestra
{
    protected Client $apiClientMock;
    protected Gmail $gmailServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var MockInterface */
        $this->apiClientMock = Mockery::mock(Client::class, [
            'setSubject' => null,
        ]);

        /** @var MockInterface */
        $this->gmailServiceMock = Mockery::mock(Gmail::class, [
            'getClient' => $this->apiClientMock,
        ]);

        /** @var MockInterface */
        $userMessagesMock = Mockery::mock(UsersMessages::class);
        $userMessagesMock->shouldReceive('send')->byDefault();

        $this->gmailServiceMock->users_messages = $userMessagesMock;

        config()->set('services.gmail_service_account.google_application_credentials', 'tests/data/test.json');
        config()->set('mail.default', 'gmail-service-account');
        config()->set('mail.mailers.gmail-service-account', [
            'transport' => 'gmail-service-account',
            'api_client' => $this->apiClientMock,
            'gmail_service' => $this->gmailServiceMock,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            GmailServiceAccountMailDriverServiceProvider::class,
        ];
    }
}
