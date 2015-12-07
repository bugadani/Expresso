<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;


class UnaryOperatorNode extends OperatorNode
{
    /**
     * @var Node
     */
    private $operand;

    public function __construct(UnaryOperator $operator, Node $operand)
    {
        parent::__construct($operator);
        $this->operand  = $operand;
    }

    public function compile(Compiler $compiler)
    {
        $this->getOperator()->compile($compiler, $this->operand);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->getOperator()->execute(
            $context,
            $this->operand
        );
    }
}