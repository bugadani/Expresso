<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class UnaryOperatorNode extends OperatorNode
{
    public function __construct(UnaryOperator $operator, Node $operand)
    {
        parent::__construct($operator);
        $this->addChild($operand);
    }
}