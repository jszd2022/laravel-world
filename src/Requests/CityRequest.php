<?php

namespace JSzD\World\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends BaseRequest {
    protected array $availableFields = [
        'id',
        'name',
        'state_id',
        'country_id',
        'country_code',
    ];
}
