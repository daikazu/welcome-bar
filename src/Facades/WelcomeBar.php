<?php

namespace Daikazu\WelcomeBar\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Daikazu\WelcomeBar\WelcomeBar
 */
class WelcomeBar extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Daikazu\WelcomeBar\WelcomeBar::class;
    }
}
