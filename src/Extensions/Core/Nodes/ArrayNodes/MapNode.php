<?php

namespace Expresso\Extensions\Core\Nodes\ArrayNodes;

use Expresso\Compiler\Node;

class MapNode extends ConstantArrayNode
{
    public function add(Node $key, Node $value)
    {
        $this->addPair($key, $value);
    }
}