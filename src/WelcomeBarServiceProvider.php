<?php

namespace Daikazu\WelcomeBar;

use Daikazu\WelcomeBar\Commands\WelcomeBarCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WelcomeBarServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('welcome-bar')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_welcome_bar_table')
            ->hasCommand(WelcomeBarCommand::class);
    }
}
