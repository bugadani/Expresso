<?php

namespace Expresso\Cache\Parsed;

use Expresso\Cache\ParsedExpressionCacheInterface;
use Expresso\Compiler\Node;

class MemoryCache implements ParsedExpressionCacheInterface
{
    private $store = [];

    public function store(string $expression, Node $parsed)
    {
        $this->store[ $expression ] = $parsed;
    }

    public function retrieve(string $expression) : Node
    {
        if (!isset($this->store[ $expression ])) {
            throw new \OutOfBoundsException("Expression has not yet been parsed: {$expression}");
        }

        return $this->store[ $expression ];
    }

    public function contains(string $expression) : bool
    {
        return isset($this->store[ $expression ]);
    }
}