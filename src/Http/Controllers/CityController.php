<?php

namespace JSzD\World\Http\Controllers;

use JSzD\World\Actions\Cities;
use JSzD\World\Requests\CityRequest;

class CityController extends Controller {
    protected string $action = Cities::class;
    protected string  $request = CityRequest::class;

    protected function getFilters() {
        return [
            'country_code' => request()->route('country_code')
        ];
    }
}
