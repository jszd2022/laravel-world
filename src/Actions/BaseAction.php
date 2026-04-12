<?php

namespace JSzD\World\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JSzD\World\Models\City;

abstract class BaseAction {
    protected string  $model;
    protected array   $defaultFields   = [];
    protected array   $availableFields = [];
    public Collection $data;
    public bool       $success         = true;

    /**
     * @param array $args
     * @return $this
     */
    public function execute(array $args = []): static {
        [
            'fields'  => $fields,
            'filters' => $filters,
            'search'  => $search,
        ] = $args + [
            'fields'  => [],
            'filters' => [],
            'search'  => null,
        ];

        $cache = $args['cache'] ?? false;

        $this->validate($fields, $filters);

        if ($cache) {
            $cacheKey = $this->genCacheKey($fields, $filters, $search);
            $this->data = Cache::remember(
                $cacheKey,
                config('laravel-world.cache_ttl'),
                fn() => $this->query($fields, $filters, $search ?? '')
            );
        } else {
            $this->data = $this->query($fields, $filters, $search ?? '');
        }

        $this->data = $this->transform($this->data);

        $this->success = !$this->data->isEmpty();

        return $this;
    }

    /**
     * @param array $fields
     * @param array $filters
     * @param string $search
     * @return mixed
     */
    protected function query(array $fields, array $filters, string $search) {
        $query = $this->model::query();
        if (empty($fields)) {
            $query->select($this->defaultFields);
        } else {
            $query->select($fields);
        }

        foreach ($filters as $field => $value) {
            $query->where($field, $value);
        }
        $query->when($search, fn($query) => $query->whereLike('name', "%{$search}%"));
        return $query->get();
    }

    /**
     * @param array $fields
     * @param array $filters
     * @return void
     */
    protected function validate(array &$fields, array &$filters) {

        $fields = Validator::make($fields, [
            '*' => ['sometimes', 'string', Rule::in($this->availableFields)]
        ])->valid();

        $filterRules = [];
        foreach ($this->availableFields as $field) {
            $filterRules[$field] = 'sometimes|string';
        }

        $filters = Validator::make($filters, $filterRules)->valid();
    }


    /**
     * @param mixed $fields
     * @param mixed $filters
     * @param mixed $search
     * @return string
     */
    private function genCacheKey(mixed $fields, mixed $filters, mixed $search): string {
        $key = "laravel-world.$this->model";
        foreach ($fields as $field) {
            $key .= ".$field";
        }
        foreach ($filters as $field => $value) {
            $key .= ".$field:$value";
        }
        if ($search) {
            $key .= ".$search";
        }
        return $key;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    protected function transform(Collection $collection) {
        return $collection;
    }
}
