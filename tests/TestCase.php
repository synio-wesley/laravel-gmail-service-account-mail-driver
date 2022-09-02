<?php

namespace Synio\GmailServiceAccountMailDriver\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Synio\GmailServiceAccountMailDriver\GmailServiceAccountMailDriverServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Synio\\GmailServiceAccountMailDriver\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            GmailServiceAccountMailDriverServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-gmail-service-account-mail-driver_table.php.stub';
        $migration->up();
        */
    }
}
