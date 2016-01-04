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
        yield $compiler->compileNode($this->getChildAt(0));
        $compiler->add('->')
                 ->add($this->getChildAt(1)->getValue());
    }

    public function evaluate(EvaluationContext $context)
    {
        $object = (yield $this->getChildAt(0)->evaluate($context));
        $method = (yield $this->getChildAt(1)->evaluate($context));

        yield [$object, $method];
    }
}