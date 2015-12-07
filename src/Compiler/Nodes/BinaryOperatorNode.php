<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class BinaryOperatorNode extends OperatorNode
{
    /**
     * @var Node
     */
    private $left;

    /**
     * @var Node
     */
    private $right;

    public function __construct(BinaryOperator $operator, Node $left, Node $right)
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
        return $this->getOperator()->evaluate($context, $this->left, $this->right);
    }

    /**
     * @return Node
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return Node
     */
    public function getRight()
    {
        return $this->right;
    }
}