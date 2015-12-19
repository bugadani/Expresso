<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class MethodNameNode extends Node
{

    public function __construct(BinaryOperatorNode $functionName)
    {
        $this->addChild($functionName->getChildAt(0));
        $this->addChild($functionName->getChildAt(1));
    }

    public function compile(Compiler $compiler)
    {
        $object = $this->getChildAt(0);
        $method = $this->getChildAt(1);

        $compiler->compileNode($object)
                 ->add('->')
                 ->add($method->getValue());
    }

    public function evaluate(EvaluationContext $context)
    {
        $object = (yield $this->getChildAt(0)->evaluate($context));
        $method = (yield $this->getChildAt(1)->evaluate($context));

        $context->setReturnValue([$object, $method]);
    }
}