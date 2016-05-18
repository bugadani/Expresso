<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class ListDataNode extends Node
{
    /**
     * @var Node[]
     */
    private $items = [];

    public function add(Node $value)
    {
        $this->items[] = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');

        foreach ($this->items as $child) {
            $compiler->add(yield $compiler->compileNode($child));
            $compiler->add(', ');
        }

        $compiler->add(']');
    }

    public function evaluate(ExecutionContext $context)
    {
        $list = [];
        foreach ($this->items as $child) {
            $list[] = (yield $child->evaluate($context));
        }

        return $list;
    }

    public function getChildren() : array
    {
        return $this->items;
    }
}