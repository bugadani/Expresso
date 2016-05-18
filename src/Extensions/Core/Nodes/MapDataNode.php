<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class MapDataNode extends Node
{
    /**
     * @var Node[]
     */
    private $keys = [];

    /**
     * @var Node[]
     */
    private $values = [];

    public function add(Node $key, Node $value)
    {
        $this->keys[]   = $key;
        $this->values[] = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');

        foreach ($this->keys as $i => $key) {
            $compiledKey   = (yield $compiler->compileNode($key));
            $compiledValue = (yield $compiler->compileNode($this->values[ $i ]));

            $compiler->add("{$compiledKey} => {$compiledValue}, ");
        }

        $compiler->add(']');
    }

    public function evaluate(ExecutionContext $context)
    {
        $array = [];

        foreach ($this->keys as $i => $key) {
            $evaluatedKey   = (yield $key->evaluate($context));
            $evaluatedValue = (yield $this->values[ $i ]->evaluate($context));

            $array [ $evaluatedKey ] = $evaluatedValue;
        }

        return $array;
    }

    public function getChildren() : array
    {
        return [$this->keys, $this->values];
    }
}