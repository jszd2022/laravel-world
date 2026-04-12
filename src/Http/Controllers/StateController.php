<?php

namespace JSzD\World\Http\Controllers;

use JSzD\World\Actions\States;
use JSzD\World\Requests\StateRequest;

class StateController extends Controller {
    protected string $action = States::class;
    protected string  $request = StateRequest::class;
    
    protected function getFilters() {
        return [
            'country_code' => request()->route('country_code')
        ];
    }
}