<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;

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

        $first = true;
        $isKey = true;
        foreach ($this->getChildren() as $child) {
            if ($first) {
                $first = false;
            } else if ($isKey) {
                $compiler->add('=>');
                $isKey = false;
            } else {
                $compiler->add(', ');
                $isKey = true;
            }
            yield $child->compile($compiler);
        }

        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $array = [];

        $isKey = true;
        foreach ($this->getChildren() as $child) {
            $value = (yield $child->evaluate($context));
            if ($isKey) {
                $key   = $value;
                $isKey = false;
            } else {
                $array [ $key ] = $value;
                $isKey          = true;
            }
        }

        $context->setReturnValue($array);
    }
}