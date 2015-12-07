<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ArrayDataNode extends Node
{
    /**
     * @var Node[]
     */
    private $keys = [];

    /**
     * @var Node[]
     */
    private $values = [];

    public function add(Node $value, Node $key)
    {
        $this->values[] = $value;
        $this->keys[]   = $key;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('[');
        foreach ($this->values as $index => $value) {
            $key = $this->keys[ $index ];
            if ($key !== DataNode::nullNode()) {
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

            if ($key !== DataNode::nullNode()) {
                $array[ $key->evaluate($context) ] = $value->evaluate($context);
            } else {
                $array[] = $value->evaluate($context);
            }
        }

        return $array;
    }
}