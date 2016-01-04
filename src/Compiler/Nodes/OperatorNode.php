<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class OperatorNode extends Node
{
    private $operator;

    public function __construct(Operator $operator)
    {
        $this->operator = $operator;
    }

    public function isOperator($class)
    {
        return $this->operator instanceof $class;
    }

    /**
     * @return Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    public function evaluate(EvaluationContext $context)
    {
        $generator = $this->getOperator()->evaluate($context, $this);

        $retVal = (yield $generator->current());
        while ($generator->valid()) {
            $retVal = (yield $generator->send($retVal));
        }
    }

    public function compile(Compiler $compiler)
    {
        $generator = $this->getOperator()->compile($compiler, $this);
        foreach ($generator as $child) {
            yield $child;
        }
    }
}