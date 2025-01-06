<?php

namespace Daikazu\WelcomeBar\Commands;

use Illuminate\Console\Command;

class WelcomeBarCommand extends Command
{
    public $signature = 'welcome-bar';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
