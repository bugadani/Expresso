<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Extensions\Core\Nodes\DataNode;

class StringNode extends DataNode
{
    public function compile(Compiler $compiler)
    {
        $compiler->compileString($this->getValue());
        yield;
    }
}