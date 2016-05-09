<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\ExpressionFunction;
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

        if (isset($functions[ $this->functionName ])) {
            $compiler->add($functions[ $this->functionName ]->getFunctionName());
        } else {
            $compiler->addVariableAccess($this->functionName);
        }

        yield;
    }

    public function evaluate(EvaluationContext $context)
    {
        $function = $context->getFunction($this->functionName);
        if ($function instanceof ExpressionFunction) {
            return $function->getFunctionName();
        } else {
            return $function;
        }
    }

    /**
     * @return mixed
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }
}