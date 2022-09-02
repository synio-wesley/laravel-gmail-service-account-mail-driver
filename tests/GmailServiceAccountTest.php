<?php

use Carbon\Carbon;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Mail;
use Synio\GmailServiceAccountMailDriver\Exceptions\AuthConfigNotFoundException;
use Synio\GmailServiceAccountMailDriver\Exceptions\InvalidGrantException;

it('throws exception when auth config path not set correctly', function () {
    config()->set('services.gmail_service_account.google_application_credentials', null);

    Mail::raw('Test body', function ($message) {
        $message->from('john@doe.com', 'John Doe');
        $message->to('jane@doe.com', 'Jane Doe');
        $message->subject('Test subject');
    });
})->throws(AuthConfigNotFoundException::class);

it('throws exception when invalid grant exception is thrown by google api', function () {
    $this->gmailServiceMock->users_messages
        ->shouldReceive('send')
        ->andThrow(InvalidGrantException::class);

    Mail::raw('Test body', function ($message) {
        $message->from('john@doe.com', 'John Doe');
        $message->to('jane@doe.com', 'Jane Doe');
        $message->subject('Test subject');
    });
})->throws(InvalidGrantException::class);

it('encodes to base64 for gmail api correctly', function () {
    $this->gmailServiceMock->users_messages
        ->shouldReceive('send')
        ->andReturnUsing(function (string $userToImpersonate, Message $gmailMessage) {
            expect($userToImpersonate)->toBe('john@doe.com');
            expect($gmailMessage->getRaw())->toBe('RnJvbTogSm9obiBEb2UgPGpvaG5AZG9lLmNvbT4NClRvOiBKYW5lIERvZSA8amFuZUBkb2UuY29tPg0KU3ViamVjdDogVGVzdCBzdWJqZWN0DQpNZXNzYWdlLUlEOiA8ZXhhbXBsZUBkb2UuY29tPg0KRGF0ZTogV2VkLCAwMiBGZWIgMjAyMiAyMjoyMjoyMiArMDAwMA0KTUlNRS1WZXJzaW9uOiAxLjANCkNvbnRlbnQtVHlwZTogdGV4dC9wbGFpbjsgY2hhcnNldD11dGYtOA0KQ29udGVudC1UcmFuc2Zlci1FbmNvZGluZzogcXVvdGVkLXByaW50YWJsZQ0KDQpUZXN0IGJvZHk');
        });

    Mail::raw('Test body', function ($message) {
        $message->from('john@doe.com', 'John Doe');
        $message->to('jane@doe.com', 'Jane Doe');
        $message->subject('Test subject');
        $message->getHeaders()->addIdHeader('Message-ID', 'example@doe.com');
        $message->getHeaders()->addDateHeader('Date', Carbon::create(2022, 2, 2, 22, 22, 22));
    });
});

it('encodes to base64 with attachment for gmail api correctly', function () {
    $this->gmailServiceMock->users_messages
        ->shouldReceive('send')
        ->andReturnUsing(function (string $userToImpersonate, Message $gmailMessage) {
            expect($userToImpersonate)->toBe('john@doe.com');
            $decoded = base64_decode(strtr($gmailMessage->getRaw(), '-_', '+/'));
            preg_match('/Content-Type: multipart\/mixed; boundary=([^\r]+)\r/', $decoded, $matches);
            if (count($matches) >= 2) {
                $decoded = str_replace($matches[1], 'LQ68zvFM', $decoded);
            }
            expect($decoded)->toBe("From: John Doe <john@doe.com>\r
To: Jane Doe <jane@doe.com>\r
Subject: Test subject\r
Message-ID: <example@doe.com>\r
Date: Wed, 02 Feb 2022 22:22:22 +0000\r
MIME-Version: 1.0\r
Content-Type: multipart/mixed; boundary=LQ68zvFM\r
\r
--LQ68zvFM\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: quoted-printable\r
\r
Test body\r
--LQ68zvFM\r
Content-Type: text/plain; name=attachment.txt\r
Content-Transfer-Encoding: base64\r
Content-Disposition: attachment; name=attachment.txt;\r
 filename=attachment.txt\r
\r
VGVzdCBhdHRhY2htZW50Cg==\r
--LQ68zvFM--\r
");
        });

    Mail::raw('Test body', function ($message) {
        $message->from('john@doe.com', 'John Doe');
        $message->to('jane@doe.com', 'Jane Doe');
        $message->subject('Test subject');
        $message->attach(__DIR__ . '/data/attachment.txt');
        $message->getHeaders()->addIdHeader('Message-ID', 'example@doe.com');
        $message->getHeaders()->addDateHeader('Date', Carbon::create(2022, 2, 2, 22, 22, 22));
    });
});
