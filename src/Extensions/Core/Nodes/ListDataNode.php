<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;

class ListDataNode extends ArrayDataNode
{
    public function add(Node $value)
    {
        $this->addPair(new DataNode($this->getElementCount()), $value);
    }
}