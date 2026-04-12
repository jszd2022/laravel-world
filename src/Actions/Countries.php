<?php

namespace JSzD\World\Actions;

use Illuminate\Support\Collection;
use JSzD\World\Contracts\WorldAction;
use JSzD\World\Models\Country;

class Countries extends BaseAction implements WorldAction {
    protected string $model           = Country::class;
    protected array  $defaultFields   = [
        'id',
        'name',
        'iso2',
    ];
    protected array  $availableFields = [
        'id',
        'iso2',
        'name',
        'phone_code',
    ];

    protected function transform(Collection $collection) {
        return $collection->map(function (Country $country) {
            if ($country->name && $country->iso2) {
                $country->name = trans("laravel-world::country.$country->iso2") ?? $country->name;
            }
            return $country;
        });
    }
}
