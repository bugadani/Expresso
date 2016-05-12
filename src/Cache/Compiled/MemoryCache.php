<?php

namespace Expresso\Cache\Compiled;

use Expresso\Cache\CompiledExpressionCacheInterface;

class MemoryCache implements CompiledExpressionCacheInterface
{
    /**
     * @var callable[]
     */
    private $store = [];

    public function store(string $expression, string $compiled) : callable
    {
        $function = eval("return {$compiled};");
        $this->store[ $expression ] = $function;

        return $function;
    }

    public function retrieve(string $expression) : callable
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