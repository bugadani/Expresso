<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ArrayDataNode extends Node
{
    public function add(Node $value, Node $key)
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

            if ($key !== DataNode::nullNode()) {
                $compiler->compileNode($key);
                $compiler->add(' => ');
            }
            $compiler->compileNode($value);
        }

        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $array = [];

        $childCount = $this->getChildCount();
        for ($i = 0; $i < $childCount; $i += 2) {
            $key   = $this->getChildAt($i);
            $value = $this->getChildAt($i + 1);

            if ($key !== DataNode::nullNode()) {
                $array[ $key->evaluate($context) ] = $value->evaluate($context);
            } else {
                $array[] = $value->evaluate($context);
            }
        }

        return $array;
    }
}