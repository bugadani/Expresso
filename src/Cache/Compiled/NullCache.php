<?php

namespace Expresso\Cache\Compiled;

use Expresso\Cache\CompiledExpressionCacheInterface;

class NullCache implements CompiledExpressionCacheInterface
{

    public function store(string $expression, string $compiled)
    {

    }

    public function retrieve(string $expression) : string
    {
        throw new \OutOfBoundsException("Expression has not yet been compiled: {$expression}");
    }

    public function contains(string $expression) : bool
    {
        return false;
    }
}