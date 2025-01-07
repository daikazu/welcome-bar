<?php

use Daikazu\WelcomeBar\Services\WelcomeBarService;

beforeEach(function (): void {
    $this->welcomeBarService = new WelcomeBarService;
});

it('returns empty array if storage path does not exist', function (): void {
    // Mock the config for the file path
    config()->set('welcome-bar.storage_path', '/non-existent-path');

    expect($this->welcomeBarService->getData())->toBe([]); // Check if it returns empty
});

it('returns data if file exists and contains valid JSON', function (): void {
    // Path to a temporary file
    $storagePath = sys_get_temp_dir() . '/welcome-bar-test.json';

    // Write valid JSON data to temporary file
    $data = ['key1' => 'value1', 'key2' => 'value2'];
    file_put_contents($storagePath, json_encode($data));

    // Mock config to use the temporary file
    config()->set('welcome-bar.storage_path', $storagePath);

    // Assert the data matches
    expect($this->welcomeBarService->getData())->toBe($data);

    // Cleanup
    unlink($storagePath);
});

it('returns empty array if file contains invalid JSON', function (): void {
    // Path to a temporary file
    $storagePath = sys_get_temp_dir() . '/welcome-bar-invalid.json';

    // Write invalid JSON to the file
    file_put_contents($storagePath, '{invalid json}');

    // Mock config to use the temporary file
    config()->set('welcome-bar.storage_path', $storagePath);

    // Assert it handles invalid JSON gracefully
    expect($this->welcomeBarService->getData())->toBe([]);

    // Cleanup
    unlink($storagePath);
});

it('stores data correctly to the file', function (): void {
    // Path to a temporary file
    $storagePath = sys_get_temp_dir() . '/welcome-bar-store.json';

    // Mock the config for the storage path
    config()->set('welcome-bar.storage_path', $storagePath);

    // Data to store
    $dataToStore = ['keyA' => 'valueA', 'keyB' => 'valueB'];

    // Call the method to store the data
    $this->welcomeBarService->storeData($dataToStore);

    // Read from file and verify
    $storedData = json_decode(file_get_contents($storagePath), true);

    // Assert the data were stored correctly
    expect($storedData)->toBe($dataToStore);

    // Cleanup
    unlink($storagePath);
});
