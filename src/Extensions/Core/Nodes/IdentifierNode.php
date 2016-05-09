<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class IdentifierNode extends Node
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addVariableAccess($this->value);
    }

    public function evaluate(EvaluationContext $context)
    {
        return yield $context[ $this->value ];
    }

    public function getName()
    {
        return $this->value;
    }
}