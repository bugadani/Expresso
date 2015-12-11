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
        $first = true;
        foreach ($this->getChildren() as $child) {
            if ($first) {
                $first = false;
            } else {
                $compiler->add(', ');
            }

            $compiler->compileNode($child);
        }
    }

    public function evaluate(EvaluationContext $context, array $childResults)
    {
        return $childResults;
    }
}