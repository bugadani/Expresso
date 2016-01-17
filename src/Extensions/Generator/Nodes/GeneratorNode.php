<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Generator\Generator\GeneratorObject;

class GeneratorNode extends Node
{

    /**
     * GeneratorNode constructor.
     *
     * @param                 $funcBody
     * @param GeneratorObject $generator
     */
    public function __construct(Node $funcBody, GeneratorObject $generator)
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