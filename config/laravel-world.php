<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Countries
    | Limit countries to be stored by specifying exceptions or only arrays; use country codes.
    |--------------------------------------------------------------------------
    */
    'countries' => [
        'only' => null,
        'except' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'enabled' => true,
        'prefix' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Migrations
    |--------------------------------------------------------------------------
    */
    'migrations' => [
        'countries' => [
            'table_name' => 'laravel-world-countries',
        ],
        'states' => [
            'table_name' => 'laravel-world-states',
        ],
        'cities' => [
            'table_name' => 'laravel-world-cities',
        ],
    ],
    
    'cache_ttl' => 604800,
];