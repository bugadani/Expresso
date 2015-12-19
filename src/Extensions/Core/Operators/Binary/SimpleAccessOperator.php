<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;

class SimpleAccessOperator extends ArrayAccessOperator
{
    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        if ($right instanceof IdentifierNode) {
            $right = new DataNode($right->getName());
        }

        return parent::createNode($config, $left, $right);
    }

    public function operators()
    {
        return '.';
    }
}