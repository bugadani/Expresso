<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\SimpleBinaryOperator;

class SimpleBinaryOperatorNode extends BinaryOperatorNode
{
    public function __construct(SimpleBinaryOperator $operator, NodeInterface $left, NodeInterface $right)
    {
        parent::__construct($operator, $left, $right);
    }
}