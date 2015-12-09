<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;


abstract class BinaryOperator extends Operator
{
    public function evaluate(EvaluationContext $context, Node $node, array $childResults)
    {
        return $this->evaluateSimple($childResults[0], $childResults[1]);
    }

    /**
     * @param $left
     * @param $right
     *
     * @return mixed
     */
    public function evaluateSimple($left, $right)
    {
        throw new \BadMethodCallException('Either evaluate or evaluateSimple must be overridden');
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add($this->compiledOperator())
                 ->compileNode($right)
                 ->add(')');
    }

    /**
     * @return string
     */
    public function compiledOperator()
    {
        throw new \BadMethodCallException('Either compile or compiledOperator must be overridden');
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }
}