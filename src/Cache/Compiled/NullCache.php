<?php

namespace Expresso\Cache\Compiled;

use Expresso\Cache\CompiledExpressionCacheInterface;

class NullCache implements CompiledExpressionCacheInterface
{

    public function store(string $expression, string $compiled) : callable
    {
        return eval("return {$compiled};");
    }

    public function retrieve(string $expression) : callable
    {
        throw new \OutOfBoundsException("Expression has not yet been compiled: {$expression}");
    }

    public function contains(string $expression) : bool
    {
        return false;
    }
}