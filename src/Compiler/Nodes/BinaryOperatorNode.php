<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class BinaryOperatorNode extends OperatorNode
{
    public function __construct(BinaryOperator $operator, Node $left, Node $right)
    {
        parent::__construct($operator);
        $this->addChild($left);
        $this->addChild($right);
    }

    public function compile(Compiler $compiler)
    {
        $this->expectChildCount(2);
        $this->getOperator()->compile($compiler, $this->getLeft(), $this->getRight());
    }

    public function evaluate(EvaluationContext $context)
    {
        $this->expectChildCount(2);
        return $this->getOperator()->evaluate($context, $this->getLeft(), $this->getRight());
    }

    /**
     * @return Node
     */
    public function getLeft()
    {
        return $this->getChildAt(0);
    }

    /**
     * @return Node
     */
    public function getRight()
    {
        return $this->getChildAt(1);
    }
}