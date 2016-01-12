<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class FunctionNameNode extends Node
{
    private $functionName;

    public function __construct($functionName)
    {
        $this->functionName = $functionName;
    }

    public function compile(Compiler $compiler)
    {
        $functions = $compiler->getConfiguration()->getFunctions();

        $compiler->add($functions[ $this->functionName ]->getFunctionName());
        yield;
    }

    public function evaluate(EvaluationContext $context)
    {
        yield $context->getFunction($this->functionName)->getFunctionName();
    }

    /**
     * @return mixed
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }
}