<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class MethodNameNode extends Node
{

    public function __construct(BinaryOperatorNode $functionName)
    {
        $this->addChild($functionName);
    }

    public function compile(Compiler $compiler)
    {
        $functionName = $this->getChildAt(0);
        $object       = $functionName->getChildAt(0);
        $method       = $functionName->getChildAt(1);

        $compiler->compileNode($object)
                 ->add('->')
                 ->add($method->getName());
    }

    public function evaluate(EvaluationContext $context)
    {
        $functionName = $this->getChildAt(0);
        $object       = $functionName->getChildAt(0)->evaluate($context);
        $methodName   = $functionName->getChildAt(1)->getName();

        return [$object, $methodName];
    }
}