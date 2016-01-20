<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class GeneratorNode extends Node
{

    /**
     * GeneratorNode constructor.
     *
     * @param Node $funcBody
     */
    public function __construct(Node $funcBody)
    {
    }

    public function compile(Compiler $compiler)
    {
        // TODO: Implement compile() method.
    }

    public function evaluate(EvaluationContext $context)
    {
        // TODO: Implement evaluate() method.
    }
}