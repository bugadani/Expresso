<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;

use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\ExecutionContext;

abstract class UnaryOperator extends Operator
{

    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = (yield $node->getOperand()->evaluate($context));

        return $this->evaluateSimple($operand);
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

        $this->compileSimple($compiler, $compiledOperand);
    }

    /**
     * @param Compiler $compiler
     * @param $operandSource
     */
    protected function compileSimple(Compiler $compiler, $operandSource)
    {
        throw new \BadMethodCallException('Either compile or compileSimple must be overridden');
    }

    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($operand) = $operands;

        return new UnaryOperatorNode($this, $operand);
    }

    public function getOperandCount() : int {
        return 1;
    }
}