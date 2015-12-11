<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\TernaryOperator;

class TernaryOperatorNode extends OperatorNode
{
    public function __construct(TernaryOperator $operator, Node $left, Node $middle, Node $right)
    {
        parent::__construct($operator);
        $this->addChild($left);
        $this->addChild($middle);
        $this->addChild($right);
    }

    public function compile(Compiler $compiler)
    {
        $this->expectChildCount(3);
        $this->getOperator()->compile(
            $compiler,
            $this->getLeft(),
            $this->getMiddle(),
            $this->getRight()
        );
    }

    /**
     * @return Node
     */
    public function getLeft()
    {
        return $this->getChildAt(0);
    }

    /**
     * @return Node
     */
    public function getMiddle()
    {
        return $this->getChildAt(1);
    }

    /**
     * @return Node
     */
    public function getRight()
    {
        return $this->getChildAt(2);
    }
}