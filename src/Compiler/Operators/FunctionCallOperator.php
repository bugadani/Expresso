<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;

class FunctionCallOperator extends Operator
{
    public function createNode($left, $right)
    {
        return new FunctionCallNode($left, $right);
    }

    public function operators()
    {

    }
}