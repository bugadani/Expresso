<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\EvaluationContext;

class ArrayDataNode extends Node
{
    /**
     * @var NodeInterface[]
     */
    private $keys = [];

    /**
     * @var NodeInterface[]
     */
    private $values = [];

    public function add(NodeInterface $value, NodeInterface $key = null)
    {
        $this->values[] = $value;
        $this->keys[]   = $key === null ? count($this->keys) : $key;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');
        foreach ($this->values as $index => $value) {
            $compiler->compileNode($this->keys[ $index ])
                     ->add(' => ')
                     ->compileNode($value)
                     ->add(',');
        }
        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $array = [];

        foreach ($this->values as $index => $value) {
            $array[ $this->keys[ $index ]->evaluate($context) ] = $value->evaluate($context);
        }

        return $array;
    }
}