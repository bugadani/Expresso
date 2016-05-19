<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\PropertyAccessNode;

class SimpleAccessOperator extends ArrayAccessOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($left, $right) = $operands;

        if ($left instanceof DataNode) {
            throw new ParseException();
        }

        return new PropertyAccessNode($left, $right);
    }
}