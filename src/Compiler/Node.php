<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\EvaluationContext;

/**
 * Class Node represents an element in the Abstract Syntax Tree.
 *
 * @package Expresso\Compiler
 */
abstract class Node
{
    protected $inline = false;

    /**
     * Sets whether this node should be compiled inline.
     *
     * @param $value
     *
     * @return $this
     */
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

    /**
     * Compile the given node.
     *
     * Note: this method should be executed by {@see Compiler).
     *
     * @param Compiler $compiler
     *
     * @return mixed
     */
    abstract public function compile(Compiler $compiler);

    /**
     * Evaluate the given node.
     *
     * Note: this method should be executed with {@see GeneratorHelper).
     *
     * @param EvaluationContext $context
     *
     * @return mixed
     */
    abstract public function evaluate(EvaluationContext $context);

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isInline()
    {
        return $this->inline;
    }
}
