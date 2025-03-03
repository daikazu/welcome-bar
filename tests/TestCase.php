<?php

namespace Daikazu\WelcomeBar\Tests;

use Daikazu\WelcomeBar\WelcomeBarServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Override;

class TestCase extends Orchestra
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Daikazu\\WelcomeBar\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    #[Override]
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }

    #[Override]
    protected function getPackageProviders($app)
    {
        return [
            WelcomeBarServiceProvider::class,
        ];
    }
}
