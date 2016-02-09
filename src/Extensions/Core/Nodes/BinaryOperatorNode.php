<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

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
        $this->left  = $left;
        $this->right = $right;
    }

    public function getChildren()
    {
        return [$this->left, $this->right];
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