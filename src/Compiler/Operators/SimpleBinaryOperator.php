<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

abstract class SimpleBinaryOperator extends BinaryOperator
{
    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add($this->compiledOperator())
                 ->compileNode($right)
                 ->add(')');
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        return $this->executeSimple(
            $left->evaluate($context),
            $right->evaluate($context)
        );
    }

    abstract public function executeSimple($left, $right);

    abstract public function compiledOperator();
}