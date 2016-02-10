<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\EvaluationContext;

class MethodNameNode extends Node
{
    /**
     * @var Node
     */
    private $object;

    /**
     * @var DataNode
     */
    private $method;

    public function __construct(BinaryOperatorNode $functionName)
    {
        list($this->object, $this->method) = $functionName->getChildren();
    }

    public function compile(Compiler $compiler)
    {
        $compiledObjectName = (yield $compiler->compileNode($this->object));

        $compiler->add($compiledObjectName)
                 ->add('->')
                 ->add($this->method->getValue());
    }

    public function evaluate(EvaluationContext $context)
    {
        $object = (yield $this->object->evaluate($context));
        $method = (yield $this->method->evaluate($context));

        yield [$object, $method];
    }

    public function getChildren()
    {
        return [$this->object, $this->method];
    }
}