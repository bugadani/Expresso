<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class UnaryOperator extends Operator
{

    public function evaluate(EvaluationContext $context, Node $node)
    {
        yield $node->getChildAt(0)->evaluate($context);
        $operand = $context->getReturnValue();

        $context->setReturnValue($this->evaluateSimple($operand));
    }

    /**
     * @param $operand
     *
     * @return mixed
     */
    public function evaluateSimple($operand)
    {
        throw new \BadMethodCallException('Either evaluate or evaluateSimple must be overridden');
    }

    abstract public function compile(Compiler $compiler, Node $operand);

    public function createNode(CompilerConfiguration $config, $operand)
    {
        return new UnaryOperatorNode($this, $operand);
    }
}