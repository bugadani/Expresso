<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
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

            $childCount = count($children);
            for ($keyIndex = 0; $keyIndex < $childCount; $keyIndex += 2) {
                $compiledKey   = (yield $compiler->compileNode($children[ $keyIndex ]));
                $compiledValue = (yield $compiler->compileNode($children[ $keyIndex + 1 ]));

                $compiler->add($compiledKey->source);
                $compiler->add(' => ');
                $compiler->add($compiledValue->source);
                $compiler->add(', ');
            }

            $compiledKey   = (yield $compiler->compileNode($lastKey));
            $compiledValue = (yield $compiler->compileNode($lastValue));

            $compiler->add($compiledKey->source);
            $compiler->add(' => ');
            $compiler->add($compiledValue->source);
        }

        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $array = [];

        $childCount = count($this->children);
        for ($keyIndex = 0; $keyIndex < $childCount; $keyIndex += 2) {
            $key   = (yield $this->children[ $keyIndex ]->evaluate($context));
            $value = (yield $this->children[ $keyIndex + 1 ]->evaluate($context));

            $array [ $key ] = $value;
        }

        yield $array;
    }

    public function getChildren()
    {
        return $this->children;
    }
}