<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\RuntimeFunction;
use Expresso\Compiler\ExpressionFunction;
use Expresso\ExecutionContext;

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
            $compiler->add("\$context->getFunction('{$this->functionName}')");
        } else {
            $compiler->addVariableAccess($this->functionName);
        }
    }

    public function evaluate(ExecutionContext $context)
    {
        $function = $context->getFunction($this->functionName);

        return $function;
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

    public function getArgumentCount() : int
    {
        return PHP_INT_MAX;
    }
}
