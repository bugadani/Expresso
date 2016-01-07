<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class MapDataNode extends Node
{
    /**
     * @var Node[]
     */
    private $children = [];

    public function add(Node $key, Node $value)
    {
        $this->children[] = $key;
        $this->children[] = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');

        if (!empty($this->children)) {
            $children = $this->children;

            $lastValue = array_pop($children);
            $lastKey   = array_pop($children);

            $items = array_chunk($children, 2);
            foreach ($items as list($key, $value)) {
                yield $compiler->compileNode($key);
                $compiler->add(' => ');
                yield $compiler->compileNode($value);
                $compiler->add(', ');
            }

            yield $compiler->compileNode($lastKey);
            $compiler->add(' => ');
            yield $compiler->compileNode($lastValue);
        }

        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $array = [];

        $isKey = true;
        foreach ($this->children as $child) {
            $value = (yield $child->evaluate($context));
            if ($isKey) {
                $key   = $value;
                $isKey = false;
            } else {
                $array [ $key ] = $value;
                $isKey          = true;
            }
        }

        yield $array;
    }

    public function getChildren()
    {
        return $this->children;
    }


}