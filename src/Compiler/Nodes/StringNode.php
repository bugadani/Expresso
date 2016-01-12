<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler\Compiler;

class StringNode extends DataNode
{
    public function compile(Compiler $compiler)
    {
        $compiler->compileString($this->getValue());
        yield;
    }
}