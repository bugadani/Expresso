<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

class UnaryOperatorNode extends Node
{
    /**
     * @var UnaryOperator
     */
    private $operator;

    /**
     * @var NodeInterface
     */
    private $operand;

    public function __construct(UnaryOperator $operator, NodeInterface $operand)
    {
        $this->operator = $operator;
        $this->operand  = $operand;
    }

    public function compile(Compiler $compiler)
    {
        $this->operator->compile($compiler, $this->operand);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->operator->execute(
            $context,
            $this->operand
        );
    }

    /**
     * @return UnaryOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }
}