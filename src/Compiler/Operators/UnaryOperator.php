<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class UnaryOperator extends Operator
{

    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = (yield $node->getOperand()->evaluate($context));

        yield $this->evaluateSimple($operand);
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

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = $node->getOperand();

        $compiledOperand = (yield $compiler->compileNode($operand));

        if ($node->isInline()) {
            $compiledOperandSource = $compiledOperand->source;
        } else {
            $compiledOperandSource = $compiler->addTempVariable($compiledOperand);
        }

        $this->compileSimple($compiler, $compiledOperandSource);
    }

    /**
     * @param Compiler $compiler
     * @param $operandSource
     */
    protected function compileSimple(Compiler $compiler, $operandSource)
    {
        throw new \BadMethodCallException('Either compile or compileSimple must be overridden');
    }

    public function createNode(CompilerConfiguration $config, $operand)
    {
        return new UnaryOperatorNode($this, $operand);
    }
}