<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\EvaluationContext;

abstract class Node
{
    protected $inline = false;

    public function setInline($value)
    {
        $this->inline = $value;

        $children = $this->getChildren();

        while (!empty($children)) {
            $child = array_pop($children);
            foreach ($child->getChildren() as $c) {
                $children[] = $c;
            }

            $child->inline = $value;
        }

        return $this;
    }

    abstract public function compile(Compiler $compiler);

    abstract public function evaluate(EvaluationContext $context);

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return [];
    }

    public function isInline()
    {
        return $this->inline;
    }
}
