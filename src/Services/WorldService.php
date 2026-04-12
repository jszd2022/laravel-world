<?php

namespace JSzD\World\Services;

use JSzD\World\Actions\BaseAction;
use JSzD\World\Actions\Cities;
use JSzD\World\Actions\Countries;
use JSzD\World\Actions\States;

class WorldService {
    protected bool $cache = false;

    public function __construct() {}

    public function withCaching(): static {
        $this->cache = true;
        return $this;
    }

    public function countries(array $args): BaseAction {
        return $this->executeAction(Countries::class, $args);
    }

    public function states(array $args): BaseAction {
        return $this->executeAction(States::class, $args);
    }

    public function cities(array $args): BaseAction {
        return $this->executeAction(Cities::class, $args);
    }

    protected function executeAction(string $action, array $args) {
        $this->consumeCache($args);
        return app($action)->execute($args);
    }

    protected function consumeCache(array &$args) {
        $args['withCaching'] = $this->cache;
        $this->cache = false;
    }
}
