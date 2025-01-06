<?php

namespace Daikazu\WelcomeBar\Commands;

use Daikazu\WelcomeBar\Services\WelcomeBarService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PruneWelcomeBarEntries extends Command
{
    protected $signature = 'welcome-bar:prune';
    protected $description = 'Remove expired welcome bar entries from the JSON storage file.';

    public function handle(WelcomeBarService $service)
    {
        $data = $service->getData();
        if (empty($data)) {
            $this->info('No data found. Nothing to prune.');

            return;
        }

        $originalCount = count($data);
        $now = Carbon::now();

        $filteredData = array_filter($data, function ($item) use ($now) {
            $expiredAt = $item['expired'] ?? null;
            if (! $expiredAt) {
                // If there's no expiration, keep it
                return true;
            }

            return $now->isBefore(Carbon::parse($expiredAt));
        });

        // Rewrite file only if we pruned something
        if (count($filteredData) !== $originalCount) {
            $service->storeData(array_values($filteredData));
            $this->info('Pruned ' . ($originalCount - count($filteredData)) . ' expired entries.');
        } else {
            $this->info('No entries pruned.');
        }
    }
}
