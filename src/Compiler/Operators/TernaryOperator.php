<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;

abstract class TernaryOperator extends Operator
{
    public function createNode(CompilerConfiguration $config, $left, $middle, $right)
    {
        return new TernaryOperatorNode($this, $left, $middle, $right);
    }
}