<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Extensions\Core\Nodes\StringNode;

class SimpleAccessOperator extends ArrayAccessOperator
{
    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        if ($right instanceof IdentifierNode) {
            $right = new StringNode($right->getName());
        }

        return parent::createNode($config, $left, $right);
    }

    public function operators()
    {
        return '.';
    }
}