<?php

namespace Expresso\Extensions\Core\Nodes\ArrayNodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class ConstantArrayNode extends Node implements \IteratorAggregate
{
    /**
     * @var Node[]
     */
    private $keys = [];

    /**
     * @var Node[]
     */
    private $values = [];

    protected function addPair(Node $key, Node $value)
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

        foreach ($this as $key => $value) {
            $evaluatedKey   = (yield $key->evaluate($context));
            $evaluatedValue = (yield $value->evaluate($context));

            $array [ $evaluatedKey ] = $evaluatedValue;
        }

        return $array;
    }

    public function getElementCount()
    {
        return count($this->values);
    }

    public function getChildren() : array
    {
        return [$this->keys, $this->values];
    }

    public function getIterator()
    {
        for ($i = 0; $i < $this->getElementCount(); $i++) {
            yield $this->keys[ $i ] => $this->values[ $i ];
        }
    }
}