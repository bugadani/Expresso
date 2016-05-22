<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Extensions\Core\Nodes\PropertyAccessNode;
use Expresso\Extensions\Core\Nodes\StringNode;

class SimpleAccessOperator extends ArrayAccessOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($left, $right) = $operands;

        if ($right instanceof IdentifierNode) {
            $right = new StringNode($right->getName());
        }
        if (!$right instanceof StringNode) {
            throw new ParseException("Access operator requires a name on the right hand");
        }
        if ($left instanceof DataNode) {
            throw new ParseException();
        }

        return new PropertyAccessNode($left, $right);
    }
}