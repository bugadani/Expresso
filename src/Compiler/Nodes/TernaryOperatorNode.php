<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;

class TernaryOperatorNode extends Node
{
    /**
     * @var TernaryOperator
     */
    private $operator;

    /**
     * @var NodeInterface
     */
    private $left;

    /**
     * @var NodeInterface
     */
    private $middle;

    /**
     * @var NodeInterface
     */
    private $right;

    public function __construct(TernaryOperator $operator, NodeInterface $left, NodeInterface $middle, NodeInterface $right)
    {
        $this->operator = $operator;
        $this->left     = $left;
        $this->middle   = $middle;
        $this->right    = $right;
    }

    public function compile(Compiler $compiler)
    {
        $this->operator->compile($compiler, $this->left, $this->middle, $this->right);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->operator->execute(
            $this->left->evaluate($context),
            $this->middle->evaluate($context),
            $this->right->evaluate($context)
        );
    }
}