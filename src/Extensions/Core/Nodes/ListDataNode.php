<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
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
                $compiledChild = (yield $compiler->compileNode($child, false));
                $compiler->add($compiledChild->source);
                $compiler->add(', ');
            }
            $compiledChild = (yield $compiler->compileNode($lastChild, false));
            $compiler->add($compiledChild->source);
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