<?php

namespace JSzD\World\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $cache = $args['withCaching'] ?? false;

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
            $query->select(array_intersect($fields, $this->availableFields));
        }

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->availableFields)) {
                $query->where($field, $value);
            }
        }
        $query->when($search, fn($query) => $query->where('name', 'like', "%{$search}%"));
        return $query->get();
    }

    /**
     * @param mixed $fields
     * @param array $filters
     * @return void
     */
    protected function validate(mixed &$fields, array &$filters) {

        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

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

        if (!empty($fields)) {
            sort($fields);
            $key .= ".fields:" . implode(',', $fields);
        }

        if (!empty($filters)) {
            ksort($filters);
            foreach ($filters as $field => $value) {
                $key .= ".filter:$field:$value";
            }
        }

        if ($search) {
            $key .= ".search:$search";
        }

        return md5($key);
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    protected function transform(Collection $collection) {
        return $collection;
    }
}
