<?php

use Daikazu\WelcomeBar\Http\Controllers\WelcomeBarController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/welcome-bar'], function () {
    // Route to fetch the current bar data
    Route::get('data', [WelcomeBarController::class, 'show'])
        ->middleware(config('welcome-bar.middleware.fetch', []));

    // Route to update the bar data from an external source
    Route::post('update', [WelcomeBarController::class, 'update'])
        ->middleware(config('welcome-bar.middleware.update', []));
});
