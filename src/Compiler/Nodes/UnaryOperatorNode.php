<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

class UnaryOperatorNode extends OperatorNode
{
    /**
     * @var NodeInterface
     */
    private $operand;

    public function __construct(UnaryOperator $operator, NodeInterface $operand)
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