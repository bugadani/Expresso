<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;


class UnaryOperatorNode extends OperatorNode
{
    public function __construct(UnaryOperator $operator, Node $operand)
    {
        parent::__construct($operator);
        $this->addChild($operand);
    }

    public function compile(Compiler $compiler)
    {
        $this->expectChildCount(1);
        $this->getOperator()->compile($compiler, $this->getChildAt(0));
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->getOperator()->evaluate(
            $context,
            $this->getChildAt(0)
        );
    }
}