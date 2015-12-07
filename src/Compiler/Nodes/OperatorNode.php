<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operator;

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
    public function getOperator()
    {
        return $this->operator;
    }
}