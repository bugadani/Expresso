<?php

namespace Expresso\Compiler;

use Expresso\EvaluationContext;

abstract class Node
{
    abstract public function compile(Compiler $compiler);

    abstract public function evaluate(EvaluationContext $context);

    public function getChildren()
    {
        return [];
    }
}
