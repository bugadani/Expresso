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
        $functionName->getChildAt(1)->addData('noEvaluate');
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

    public function evaluate(EvaluationContext $context, array $childResults)
    {
        $functionName = $this->getChildAt(0);
        $object       = $childResults[0];
        $methodName   = $functionName->getChildAt(1)->getName();

        return [$object, $methodName];
    }
}