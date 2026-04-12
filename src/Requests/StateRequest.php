<?php

namespace JSzD\World\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends BaseRequest {
    protected array $availableFields = [
        'id',
        'name',
        'country_id',
        'country_code',
        'state_code'
    ];
}
