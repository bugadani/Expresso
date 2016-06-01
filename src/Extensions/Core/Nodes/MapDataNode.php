<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;

class MapDataNode extends ArrayDataNode
{
    public function add(Node $key, Node $value)
    {
        $this->addPair($key, $value);
    }
}