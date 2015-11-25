<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\ExecutionContext;

class BinaryOperatorNode extends Node
{
    /**
     * @var BinaryOperator
     */
    private $operator;

    /**
     * @var NodeInterface
     */
    private $left;

    /**
     * @var NodeInterface
     */
    private $right;

    public function __construct(BinaryOperator $operator, NodeInterface $left, NodeInterface $right)
    {
        $this->operator = $operator;
        $this->left     = $left;
        $this->right    = $right;
    }

    public function compile(Compiler $compiler)
    {
        $this->operator->compile($compiler, $this->left, $this->right);
    }

    public function evaluate(ExecutionContext $context)
    {
        return $this->operator->execute($context, $this->left, $this->right);
    }

    /**
     * @return BinaryOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }
}