<?php

namespace JSzD\World\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends BaseRequest {
    protected array $availableFields = [
        'id',
        'iso2',
        'name',
        'phone_code'
    ];
}
