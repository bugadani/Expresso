<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\ExpressionFunction;

use Expresso\EvaluationContext;

class FunctionNameNode extends CallableNode
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

    public function inlineable() : bool
    {
        return true;
    }
}