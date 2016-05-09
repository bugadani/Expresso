<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;

abstract class CallableNode extends Node
{
    public function inlineable() : bool {
        return false;
    }
}