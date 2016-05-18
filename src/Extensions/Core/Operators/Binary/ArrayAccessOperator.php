<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\ArrayAccessNode;

class ArrayAccessOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands) : Node
    {
        list($left, $right) = $operands;

        return new ArrayAccessNode($left, $right);
    }
}