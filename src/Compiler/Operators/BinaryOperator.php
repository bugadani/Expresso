<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;

use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class BinaryOperator extends Operator
{
    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var Node $left */
        /** @var Node $right */
        list($left, $right) = $node->getChildren();

        $leftOperand  = (yield $left->evaluate($context));
        $rightOperand = (yield $right->evaluate($context));

        yield $this->evaluateSimple($leftOperand, $rightOperand);
    }

    /**
     * @param $left
     * @param $right
     *
     * @return mixed
     */
    protected function evaluateSimple($left, $right)
    {
        throw new \BadMethodCallException('Either evaluate or evaluateSimple must be overridden');
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $leftOperand  = (yield $compiler->compileNode($left));
        $rightOperand = (yield $compiler->compileNode($right));

        $this->compileSimple($compiler, $leftOperand, $rightOperand);
    }

    /**
     * @param Compiler $compiler
     * @param          $leftSource
     * @param          $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add("({$leftSource} {$this->compiledOperator()} {$rightSource})");
    }

    /**
     * @return string
     */
    protected function compiledOperator()
    {
        throw new \BadMethodCallException('Either compile or compiledOperator must be overridden');
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }
}