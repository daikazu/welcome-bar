<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Storage Path
      |--------------------------------------------------------------------------
      */
    'storage_path' => storage_path('app/welcome-bar.json'),

    /*
    |--------------------------------------------------------------------------
    | Route Middlewares or Security
    |--------------------------------------------------------------------------
    |
    | For example, you could specify a route middleware group here that your
    | "update" route uses for authentication or token checking.
    |
    */
    'middleware' => [
        'update' => ['api'], // Or anything your app uses, e.g. 'auth:api', or a custom 'welcome-bar-auth'
        'fetch'  => ['web'], // Or 'api'
    ],
];
