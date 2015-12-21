<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class DataNode extends Node
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addData($this->value);
        yield;
    }

    public function evaluate(EvaluationContext $context)
    {
        $context->setReturnValue($this->value);
        yield;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}