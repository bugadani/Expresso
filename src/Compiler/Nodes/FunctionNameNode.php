<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
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
    }

    public function evaluate(EvaluationContext $context, array $childResults, NodeTreeEvaluator $evaluator)
    {
        return $context->getFunction($this->functionName)->getFunctionName();
    }
}