<?php

namespace Expresso\Cache;

interface CompiledExpressionCacheInterface
{
    public function store(string $expression, string $compiled) : callable;

    public function retrieve(string $expression) : callable;

    public function contains(string $expression) : bool;
}