<?php

namespace JSzD\World\Contracts;

interface WorldAction {
    public function execute(array $args): static;
}
