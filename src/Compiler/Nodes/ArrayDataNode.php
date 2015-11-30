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
        $this->keys[]   = $key;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');
        foreach ($this->values as $index => $value) {
            $key = $this->keys[ $index ];
            if ($key instanceof NodeInterface) {
                $compiler->compileNode($this->keys[ $index ]);
                $compiler->add(' => ');
            }
            $compiler->compileNode($value)
                     ->add(',');
        }
        $compiler->add(']');
    }

    public function evaluate(EvaluationContext $context)
    {
        $array = [];

        foreach ($this->values as $index => $value) {
            $key = $this->keys[ $index ];

            if ($key instanceof NodeInterface) {
                $array[ $key->evaluate($context) ] = $value->evaluate($context);
            } else {
                $array[] = $value->evaluate($context);
            }
        }

        return $array;
    }
}