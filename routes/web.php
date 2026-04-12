<?php

use JSzD\World\Http\Controllers;

Route::group([
    'prefix'     => config('laravel-world.routes.prefix'),
    'middleware' => ['throttle:60,1'],
], function () {
    if (config('laravel-world.routes.enabled')) {
        Route::get('/countries', [Controllers\CountryController::class, 'index'])->name('countries.index');
        Route::get('/countries/{country_code}/states', [Controllers\StateController::class, 'index'])->name('states.index');
        Route::get('/countries/{country_code}/cities', [Controllers\CityController::class, 'index'])->name('cities.index');
    }
});
