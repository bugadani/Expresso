<?php

namespace Expresso\Cache\Parsed;

use Expresso\Cache\ParsedExpressionCacheInterface;
use Expresso\Compiler\Node;

class NullCache implements ParsedExpressionCacheInterface
{

    public function store(string $expression, Node $parsed)
    {

    }

    public function retrieve(string $expression) : Node
    {
        throw new \OutOfBoundsException("Expression has not yet been parsed: {$expression}");
    }

    public function contains(string $expression) : bool
    {
        return false;
    }
}