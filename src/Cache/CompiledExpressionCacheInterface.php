<?php

namespace Expresso\Cache;

interface CompiledExpressionCacheInterface
{
    public function store(string $expression, string $compiled);

    public function retrieve(string $expression) : string;

    public function contains(string $expression) : bool;
}