<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class UnaryOperatorNode extends OperatorNode
{
    /**
     * @var Node
     */
    private $operand;

    public function __construct(UnaryOperator $operator, Node $operand)
    {
        parent::__construct($operator);
        $this->operand = $operand;
    }

    /**
     * @return Node
     */
    public function getChildren() : array
    {
        return [$this->operand];
    }

    /**
     * @return Node
     */
    public function getOperand()
    {
        return $this->operand;
    }
}