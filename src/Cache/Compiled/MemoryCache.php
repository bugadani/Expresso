<?php

namespace Expresso\Cache\Compiled;

use Expresso\Cache\CompiledExpressionCacheInterface;

class MemoryCache implements CompiledExpressionCacheInterface
{
    private $store = [];

    public function store(string $expression, string $compiled)
    {
        $this->store[ $expression ] = $compiled;
    }

    public function retrieve(string $expression) : string
    {
        if (!isset($this->store[ $expression ])) {
            throw new \OutOfBoundsException("Expression has not yet been compiled: {$expression}");
        }

        return $this->store[ $expression ];
    }

    public function contains(string $expression) : bool
    {
        return isset($this->store[ $expression ]);
    }
}