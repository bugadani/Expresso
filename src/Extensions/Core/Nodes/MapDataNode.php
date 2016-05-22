<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class MapDataNode extends ArrayDataNode
{
    public function add(Node $key, Node $value)
    {
        $this->addPair($key, $value);
    }
}