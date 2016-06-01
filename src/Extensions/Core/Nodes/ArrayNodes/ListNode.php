<?php

namespace Expresso\Extensions\Core\Nodes\ArrayNodes;

use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\DataNode;

class ListNode extends ConstantArrayNode
{
    public function add(Node $value)
    {
        $this->addPair(new DataNode($this->getElementCount()), $value);
    }
}