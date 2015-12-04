<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;

class TernaryOperatorNode extends OperatorNode
{
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
        parent::__construct($operator);
        $this->left     = $left;
        $this->middle   = $middle;
        $this->right    = $right;
    }

    public function compile(Compiler $compiler)
    {
        $this->getOperator()->compile($compiler, $this->left, $this->middle, $this->right);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->getOperator()->execute(
            $context,
            $this->left,
            $this->middle,
            $this->right
        );
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
    public function getMiddle()
    {
        return $this->middle;
    }

    /**
     * @return NodeInterface
     */
    public function getRight()
    {
        return $this->right;
    }
}