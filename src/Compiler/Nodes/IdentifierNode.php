<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\EvaluationContext;

class IdentifierNode extends Node
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addVariableAccess($this->value);
    }

    public function evaluate(EvaluationContext $context)
    {
        $context->setReturnValue($context[ $this->value ]);
        yield;
    }

    public function getName()
    {
        return $this->value;
    }
}