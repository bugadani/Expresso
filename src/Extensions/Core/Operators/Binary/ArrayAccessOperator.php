<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\ArrayAccessNode;
use Expresso\Extensions\Core\Nodes\DataNode;

class ArrayAccessOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands) : Node
    {
        list($left, $right) = $operands;

        if ($left instanceof DataNode) {
            throw new ParseException();
        }

        return new ArrayAccessNode($left, $right);
    }
}