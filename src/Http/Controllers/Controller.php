<?php

namespace JSzD\World\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller {
    protected string $action;
    protected string $request;


    abstract protected function getFilters();

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $request = app($this->request);

        $validated = $request->validated();

        $validated['filters'] = array_merge($validated['filters'] ?? [], $this->getFilters());

        $data = app($this->action)
            ->execute($validated);

        return response()->json($data);
    }
}
