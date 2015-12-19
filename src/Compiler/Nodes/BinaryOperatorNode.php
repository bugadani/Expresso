<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class BinaryOperatorNode extends OperatorNode
{
    public function __construct(BinaryOperator $operator, Node $left, Node $right)
    {
        parent::__construct($operator);
        $this->addChild($left);
        $this->addChild($right);
    }
}