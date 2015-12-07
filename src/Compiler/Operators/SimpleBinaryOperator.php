<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;


abstract class SimpleBinaryOperator extends BinaryOperator
{
    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add($this->compiledOperator())
                 ->compileNode($right)
                 ->add(')');
    }

    public function execute(EvaluationContext $context, Node $left, Node $right)
    {
        return $this->executeSimple(
            $left->evaluate($context),
            $right->evaluate($context)
        );
    }

    abstract public function executeSimple($left, $right);

    abstract public function compiledOperator();
}