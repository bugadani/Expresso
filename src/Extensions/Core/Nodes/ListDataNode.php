<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

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

        if (!empty($this->items)) {
            $children  = $this->items;
            $lastChild = array_pop($children);

            foreach ($children as $child) {
                yield $compiler->compileNode($child);
                $compiler->add(', ');
            }
            yield $compiler->compileNode($lastChild);
        }

        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $list = [];
        foreach ($this->items as $child) {
            $list[] = (yield $child->evaluate($context));
        }
        yield $list;
    }

    public function getChildren()
    {
        return $this->items;
    }
}