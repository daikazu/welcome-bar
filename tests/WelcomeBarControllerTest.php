<?php

use Daikazu\WelcomeBar\Http\Controllers\WelcomeBarController;
use Daikazu\WelcomeBar\Services\WelcomeBarService;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->mockService = Mockery::mock(WelcomeBarService::class);
    $this->controller = new WelcomeBarController($this->mockService);
});

it('returns welcome bar data in the show method', function (): void {
    $mockData = [
        ['message' => 'Welcome to our site!'],
        ['message' => 'Free shipping on all orders!'],
    ];

    $this->mockService
        ->shouldReceive('getData')
        ->once()
        ->andReturn($mockData);

    $response = $this->controller->show();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toMatchArray($mockData);
});

it('returns success status in the ping method', function (): void {
    $response = $this->controller->ping();

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toMatchArray(['status' => 'success']);
});

it('validates and saves data in the update method', function (): void {
    $requestData = [
        ['message' => 'Holiday sale starts today!'],
        ['message' => 'Exclusive offers available.'],
    ];

    $validatedDataMock = Mockery::mock(Request::class)
        ->shouldReceive('validate')
        ->once()
        ->with(['*.message' => 'required|string'])
        ->andReturn($requestData)
        ->getMock();

    $validatedDataMock
        ->shouldReceive('all')
        ->once()
        ->andReturn($requestData);

    $this->mockService
        ->shouldReceive('storeData')
        ->once()
        ->with($requestData);

    $response = $this->controller->update($validatedDataMock);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toMatchArray([
            'status'  => 'success',
            'message' => 'Welcome Bar data updated',
        ]);
});
