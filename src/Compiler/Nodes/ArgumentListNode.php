<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ArgumentListNode extends Node
{
    public function __construct(array $children)
    {
        array_map([$this, 'addChild'], $children);
    }

    public function compile(Compiler $compiler)
    {
        if ($this->getChildCount() > 0) {
            $children  = $this->getChildren();
            $lastChild = array_pop($children);

            foreach ($children as $child) {
                yield $child->compile($compiler);
                $compiler->add(', ');
            }
            yield $lastChild->compile($compiler);
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