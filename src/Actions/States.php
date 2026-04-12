<?php

namespace JSzD\World\Actions;

use JSzD\World\Contracts\WorldAction;
use JSzD\World\Models\State;

class States extends BaseAction implements WorldAction {
    protected string $model           = State::class;
    protected array  $defaultFields   = [
        'id',
        'name',
    ];
    protected array  $availableFields = [
        'id',
        'name',
        'country_id',
        'country_code',
        'state_code',
    ];
}
