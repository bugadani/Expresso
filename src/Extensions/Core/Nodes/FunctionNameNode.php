<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Runtime\ExecutionContext;

class FunctionNameNode extends CallableNode
{
    private $functionName;

    public function __construct(string $functionName)
    {
        $this->functionName = $functionName;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add("\$context->getFunction(")
                 ->compileString($this->functionName)
                 ->add(')');
    }

    public function evaluate(ExecutionContext $context)
    {
        return $context->getFunction($this->functionName);
    }

    public function inlineable() : bool
    {
        return true;
    }
}
