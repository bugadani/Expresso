<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ArgumentListNode extends Node
{
    public function compile(Compiler $compiler)
    {
        if ($this->getChildCount() > 0) {
            $children  = $this->getChildren();
            $lastChild = array_pop($children);

            foreach ($children as $child) {
                yield $compiler->compileNode($child);
                $compiler->add(', ');
            }
            yield $compiler->compileNode($lastChild);
        }
    }

    public function evaluate(EvaluationContext $context)
    {
        $list = [];
        foreach ($this->getChildren() as $child) {
            $list[] = (yield $child->evaluate($context));
        }
        $context->setReturnValue($list);
    }
}