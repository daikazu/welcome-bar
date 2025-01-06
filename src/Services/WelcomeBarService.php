<?php

namespace Daikazu\WelcomeBar\Services;

class WelcomeBarService
{
    public function getData(): array
    {
        $storagePath = config('welcome-bar.storage_path');

        if (! file_exists($storagePath)) {
            return [];
        }

        $contents = file_get_contents($storagePath);
        $data = json_decode($contents, true);

        return is_array($data) ? $data : [];
    }

    public function storeData(array $data): void
    {
        $storagePath = config('welcome-bar.storage_path');

        // Write the file to disk
        file_put_contents(
            $storagePath,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }
}
