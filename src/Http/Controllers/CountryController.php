<?php

namespace JSzD\World\Http\Controllers;

use JSzD\World\Actions\Countries;
use JSzD\World\Requests\CountryRequest;

class CountryController extends Controller {
    protected string $action = Countries::class;
    protected string  $request = CountryRequest::class;
    
    protected function getFilters() {
        return [];
    }
}