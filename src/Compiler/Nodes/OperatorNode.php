<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operator;
use Expresso\Runtime\ExecutionContext;

abstract class OperatorNode extends Node
{
    private $operator;

    public function __construct(Operator $operator)
    {
        $this->operator = $operator;
    }

    public function isOperator($class)
    {
        return $this->operator instanceof $class;
    }

    /**
     * @return Operator
     */
    public function getOperator() : Operator
    {
        return $this->operator;
    }

    public function evaluate(ExecutionContext $context)
    {
        return $this->operator->evaluate($context, $this);
    }

    public function compile(Compiler $compiler)
    {
        return $this->operator->compile($compiler, $this);
    }
}