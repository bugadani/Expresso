<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Leaf;
use Expresso\ExecutionContext;

class IdentifierNode extends Leaf
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

    public function evaluate(ExecutionContext $context)
    {
        return $context[$this->value];
    }

    public function getName()
    {
        return $this->value;
    }
}