<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\ExecutionContext;
use Expresso\Compiler\Leaf;

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
}