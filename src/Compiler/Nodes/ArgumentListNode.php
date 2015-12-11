<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
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
                $compiler->compileNode($child)
                         ->add(', ');
            }
            $compiler->compileNode($lastChild);
        }
    }

    public function evaluate(EvaluationContext $context, array $childResults, NodeTreeEvaluator $evaluator)
    {
        return $childResults;
    }
}