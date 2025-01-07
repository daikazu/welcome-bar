<?php

namespace Daikazu\WelcomeBar;

use Composer\InstalledVersions;
use Daikazu\WelcomeBar\Commands\PruneWelcomeBarEntries;
use Daikazu\WelcomeBar\View\Components\WelcomeBar;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Override;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WelcomeBarServiceProvider extends PackageServiceProvider
{
    #[Override]
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
            ->hasCommand(PruneWelcomeBarEntries::class)
            ->hasRoutes('welcome-bar');
    }

    #[Override]
    public function packageBooted()
    {

        Blade::component('welcome-bar', WelcomeBar::class);

        AboutCommand::add('Welcome Bar', fn () => [
            'description' => 'A welcome bar for your Laravel app.',
            'version'     => InstalledVersions::getPrettyVersion('daikazu/welcome-bar'),
        ]);
    }
}
