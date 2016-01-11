<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
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

        if ($this->isInline()) {
            $objNameVar = $compiledObjectName->source;
        } else {
            $objNameVar = $compiler->addTempVariable($compiledObjectName);
        }
        $compiler->add($objNameVar)
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