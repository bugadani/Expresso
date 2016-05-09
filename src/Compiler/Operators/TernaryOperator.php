<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;

abstract class TernaryOperator extends Operator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands) : Node
    {
        list($left, $middle, $right) = $operands;
        return new TernaryOperatorNode($this, $left, $middle, $right);
    }

    public function getOperandCount() : int {
        return 3;
    }
}