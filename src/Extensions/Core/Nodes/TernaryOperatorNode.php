<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\OperatorNode;
use Expresso\Compiler\Operators\TernaryOperator;

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

        $this->left   = $left;
        $this->middle = $middle;
        $this->right  = $right;
    }

    public function getChildren()
    {
        return [
            $this->left,
            $this->middle,
            $this->right
        ];
    }
}