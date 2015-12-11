<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\EvaluationContext;

class MapDataNode extends Node
{
    public function add(Node $key, Node $value)
    {
        $this->addChild($key);
        $this->addChild($value);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');

        $childCount = $this->getChildCount();
        $first      = true;
        for ($i = 0; $i < $childCount; $i += 2) {
            if ($first) {
                $first = false;
            } else {
                $compiler->add(', ');
            }
            $key   = $this->getChildAt($i);
            $value = $this->getChildAt($i + 1);

            $compiler->compileNode($key)
                     ->add(' => ')
                     ->compileNode($value);
        }

        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context, array $childResults, NodeTreeEvaluator $evaluator)
    {
        $array = [];

        $childCount = count($childResults);
        for ($i = 0; $i < $childCount; $i += 2) {
            $key   = $childResults[ $i ];
            $value = $childResults[ $i + 1 ];

            $array[ $key ] = $value;
        }

        return $array;
    }
}