<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
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
        yield $this->getChildAt(0)->evaluate($context);
        $object = $context->getReturnValue();
        yield $this->getChildAt(1)->evaluate($context);
        $method = $context->getReturnValue();

        $context->setReturnValue([$object, $method]);
    }
}