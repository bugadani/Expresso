<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;

class TernaryOperatorNode extends OperatorNode
{
    /**
     * @var Node
     */
    private $left;

    /**
     * @var Node
     */
    private $middle;

    /**
     * @var Node
     */
    private $right;

    public function __construct(TernaryOperator $operator, Node $left, Node $middle, Node $right)
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
     * @return Node
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return Node
     */
    public function getMiddle()
    {
        return $this->middle;
    }

    /**
     * @return Node
     */
    public function getRight()
    {
        return $this->right;
    }
}