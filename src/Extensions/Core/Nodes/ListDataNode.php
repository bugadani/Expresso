<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ListDataNode extends Node
{
    public function add(Node $value)
    {
        $this->addChild($value);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');

        if ($this->getChildCount() > 0) {
            $children  = $this->getChildren();
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
        foreach ($this->getChildren() as $child) {
            $list[] = (yield $child->evaluate($context));
        }
        yield $list;
    }
}