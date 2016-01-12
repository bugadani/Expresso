<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
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
        yield;
    }

    public function evaluate(EvaluationContext $context)
    {
        yield $context[ $this->value ];
    }

    public function getName()
    {
        return $this->value;
    }
}