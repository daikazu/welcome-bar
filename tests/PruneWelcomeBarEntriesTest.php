<?php

use Daikazu\WelcomeBar\Services\WelcomeBarService;
use Illuminate\Support\Carbon;

it('shows a message if there is no data to prune', function (): void {
    // Arrange
    $service = Mockery::mock(WelcomeBarService::class);
    $service->shouldReceive('getData')->once()->andReturn([]); // Expect `getData` to be called once and return an empty array
    $service->shouldNotReceive('storeData'); // Assert `storeData` should not be called

    // Use Laravel's binding to ensure the mock is injected correctly
    $this->app->instance(WelcomeBarService::class, $service);

    // Act & Assert
    $this->artisan('welcome-bar:prune')
        ->expectsOutput('No data found. Nothing to prune.')
        ->assertExitCode(0);
});

it('prunes expired entries and shows the appropriate message', function (): void {
    // Arrange
    $currentDate = Carbon::now();
    Carbon::setTestNow($currentDate); // Freeze time for consistent testing

    $service = Mockery::mock(WelcomeBarService::class);
    $data = [
        ['expired' => $currentDate->copy()->subDay()->toIso8601String()], // Expired yesterday
        ['expired' => $currentDate->copy()->addDay()->toIso8601String()], // Expires tomorrow
        ['expired' => null], // No expiration
    ];
    $expectedPrunedData = [
        ['expired' => $currentDate->copy()->addDay()->toIso8601String()], // Expires tomorrow
        ['expired' => null], // No expiration
    ];

    $service->shouldReceive('getData')->once()->andReturn($data);
    $service->shouldReceive('storeData')->once()->with($expectedPrunedData);
    $this->app->instance(WelcomeBarService::class, $service); // Bind the mock service to the app

    // Act & Assert
    $this->artisan('welcome-bar:prune')
        ->expectsOutput('Pruned 1 expired entries.')
        ->assertExitCode(0);
});

it('does not prune if no entries are expired', function (): void {
    // Arrange
    $currentDate = Carbon::now();
    Carbon::setTestNow($currentDate); // Freeze time for consistent testing

    $service = Mockery::mock(WelcomeBarService::class);
    $data = [
        ['expired' => $currentDate->copy()->addDay()->toIso8601String()], // Expires tomorrow
        ['expired' => $currentDate->copy()->addWeek()->toIso8601String()], // Expires in a week
        ['expired' => null], // No expiration
    ];

    $service->shouldReceive('getData')->once()->andReturn($data); // Mock `getData` and return test data with no expired entries
    $service->shouldNotReceive('storeData'); // Ensure `storeData` is not called

    // Bind the mock to Laravel's service container
    $this->app->instance(WelcomeBarService::class, $service);

    // Act & Assert
    $this->artisan('welcome-bar:prune')
        ->expectsOutput('No entries pruned.')
        ->assertExitCode(0);
});
