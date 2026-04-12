<?php

namespace JSzD\World\Services;

use JSzD\World\Actions\Countries;
use JSzD\World\Contracts\WorldAction;

class WorldService {
    protected bool $cache = false;

    public function __construct() {}

    public function cache(): static {
        $this->cache = true;
        return $this;
    }

    public function countries(array $args): WorldAction {
        return $this->executeAction(Countries::class, $args);
    }

    public function states(array $args): WorldAction {
        return $this->executeAction(Countries::class, $args);
    }

    public function cities(array $args): WorldAction {
        return $this->executeAction(Countries::class, $args);
    }

    protected function executeAction(string $action, array $args) {
        $this->consumeCache($args);
        return app($action)->execute($args);
    }

    protected function consumeCache(array &$args) {
        $args['cache'] = $this->cache;
        $this->cache = false;
    }
}
