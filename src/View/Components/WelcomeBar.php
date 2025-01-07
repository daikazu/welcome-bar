<?php

namespace Daikazu\WelcomeBar\View\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;
use Illuminate\View\View;
use Override;

class WelcomeBar extends Component
{
    #[Override]
    public function render(): Application|Factory|\Illuminate\Contracts\View\View|Htmlable|string|Closure|View
    {
        return view('welcome-bar::welcome-bar');
    }
}
