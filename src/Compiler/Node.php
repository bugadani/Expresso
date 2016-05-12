<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

/**
 * Class Node represents an element in the Abstract Syntax Tree.
 *
 * @package Expresso\Compiler
 */
abstract class Node
{

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
     * @param ExecutionContext $context
     * @return mixed
     */
    abstract public function evaluate(ExecutionContext $context);

    /**
     * @return Node[]
     */
    public function getChildren() : array
    {
        return [];
    }
}
