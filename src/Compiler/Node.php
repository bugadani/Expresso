<?php

namespace Expresso\Compiler;

use Expresso\EvaluationContext;

abstract class Node
{
    /**
     * @var Node[]
     */
    private $children = [];

    abstract public function compile(Compiler $compiler);

    abstract public function evaluate(EvaluationContext $context);

    /**
     * @param Node $child
     */
    public function addChild(Node $child)
    {
        $this->children[] = $child;
    }

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getChildAt($index)
    {
        return $this->children[ $index ];
    }

    /**
     * @return array
     */
    public function getChildCount()
    {
        return count($this->children);
    }

    public function expectChildCount($count)
    {
        $actualCount = count($this->children);
        if (!$actualCount === $count) {
            throw new \UnexpectedValueException("This node expects {$count} children but has {$actualCount}");
        }
    }
}
