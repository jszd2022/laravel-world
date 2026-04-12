<?php

namespace JSzD\World\Actions;

use JSzD\World\Contracts\WorldAction;
use JSzD\World\Models\City;

class Cities extends BaseAction implements WorldAction {
    protected string $model         = City::class;
    protected array  $defaultFields = [
        'id',
        'name',
    ];
    protected array $availableFields = [
        'id',
        'name',
        'state_id',
        'country_id',
        'country_code',
    ];
}