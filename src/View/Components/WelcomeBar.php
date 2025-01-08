<?php

namespace Daikazu\WelcomeBar\View\Components;

use Carbon\Carbon;
use Closure;
use Daikazu\WelcomeBar\Services\WelcomeBarService;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
use Override;

class WelcomeBar extends Component
{
    public Collection $bars;

    public function __construct(WelcomeBarService $welcomeBarService)
    {
        // Get the array of welcome bars (from storage or wherever your service fetches it).
        $bars = $welcomeBarService->getData();

        // Filter bars by "schedule.start" and "schedule.end".
        // We'll assume "start" and "end" are either valid ISO date strings or missing.
        // Only show bars where now >= start AND now <= end
        $now = now();

        $this->bars = collect($bars)->filter(function ($bar) use ($now) {
            // If no schedule object, assume it's always valid
            if (! isset($bar['schedule'])) {
                return true;
            }

            $start = $bar['schedule']['start'] ?? null;
            $end = $bar['schedule']['end'] ?? null;

            // Convert to Carbon if present
            if ($start) {
                $start = Carbon::parse($start);
                if ($now->lt($start)) {
                    return false;
                }
            }
            if ($end) {
                $end = Carbon::parse($end);
                if ($now->gt($end)) {
                    return false;
                }
            }

            return true;
        })
            ->values(); // Re-index the collection

    }

    #[Override]
    public function render(): Application|Factory|\Illuminate\Contracts\View\View|Htmlable|string|Closure|View
    {
        return view('welcome-bar::welcome-bar');
    }
}
