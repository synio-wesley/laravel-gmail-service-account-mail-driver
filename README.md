# Use GMail API to send mails from any user/mailbox of your Workspace organization using a service account without needing separate mailbox credentials

[![Latest Version on Packagist](https://img.shields.io/packagist/v/synio/laravel-gmail-service-account-mail-driver.svg?style=flat)](https://packagist.org/packages/synio/laravel-gmail-service-account-mail-driver)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/synio-wesley/laravel-gmail-service-account-mail-driver/run-tests?label=tests&style=flat)](https://github.com/synio-wesley/laravel-gmail-service-account-mail-driver/actions?query=workflow%3Arun-tests+branch%3Amain)
[![License](https://img.shields.io/github/license/synio-wesley/laravel-gmail-service-account-mail-driver.svg?style=flat)](https://github.com/synio-wesley/laravel-gmail-service-account-mail-driver/blob/main/LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/synio/laravel-gmail-service-account-mail-driver.svg?style=flat-square)](https://packagist.org/packages/synio/laravel-gmail-service-account-mail-driver)

This Laravel package adds a mail driver for the GMail API using a service account. This makes it possible to send mails using the actual mailbox of any user/mailbox in your Google Workspace, without requiring additional App Passwords or other workarounds.

## Installation

You can install the package via composer:

```bash
composer require synio/laravel-gmail-service-account-mail-driver
```

### Setup at Google

*I initially used [this guide](https://ebstalimited.zendesk.com/hc/en-us/articles/360017031473-How-to-a-create-a-Gmail-service-account) with screenshots to perform these operations. It might help you out better.*

Before you can use this package, you need to configure the following in Google Cloud Platform Console:

- Enable GMail API for a project
- Create service account user
- Create JSON type key for this service account user
- Download and save the generated JSON file
- Write down the `client_id` (or take it from inside the JSON file)

And then you have to configure 'Domain wide delegation' in Google Workspace Admin:

- Go to *Security -> API controls -> Domain wide delegation -> Manage domain wide delegation*
- Add new API client
  - Input the Client ID you wrote down
  - Input the following OAuth scope: `https://www.googleapis.com/auth/gmail.send`
  - Confirm by clicking on *Authorize*

### Setup in your app

After you downloaded a JSON type key file, you should put the JSON file somewhere and you should let the package know the path in one of the following ways:

- Define `GMAIL_SERVICE_ACCOUNT_GOOGLE_APPLICATION_CREDENTIALS` in your `.env` file. The value should be a filename relative to the root directory of your app, or an absolute path
- Alternatively, you can also update your `services.php` config file, and add something like this:

    ```php
        'gmail_service_account' => [
            'google_application_credentials' => '/path-to-json-file',
        ],
    ```

You also have to add the mailer configuration to your `mail.php` config file:

```php
    'mailers' => [
        // ...Existing mailers here...

        'gmail-service-account' => [
            'transport' => 'gmail-service-account',
        ],
    ],
```

## Usage

To enable this mail driver globally by default, you can set `MAIL_MAILER` to `gmail-service-account` in your `.env` file.

Any mails sent using the `gmail-service-account` driver using the Laravel framework (`Mail` facade or `Mailables` for example) will now be sent using the configured GMail service account.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Wesley Stessens](https://github.com/synio-wesley)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
