<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class BinaryOperatorNode extends OperatorNode
{
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
        parent::__construct($operator);
        $this->left     = $left;
        $this->right    = $right;
    }

    public function compile(Compiler $compiler)
    {
        $this->getOperator()->compile($compiler, $this->left, $this->right);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->getOperator()->execute($context, $this->left, $this->right);
    }

    /**
     * @return NodeInterface
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return NodeInterface
     */
    public function getRight()
    {
        return $this->right;
    }
}