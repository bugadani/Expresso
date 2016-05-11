<?php

namespace Expresso\Cache;

use Expresso\Compiler\Node;

interface ParsedExpressionCacheInterface
{
    public function store(string $expression, Node $parsed);

    public function retrieve(string $expression) : Node;

    public function contains(string $expression) : bool;
}