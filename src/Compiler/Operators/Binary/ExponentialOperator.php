<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\ExecutionContext;

class ExponentialOperator extends BinaryOperator
{

    public function operators()
    {
        return '^';
    }

    public function execute(ExecutionContext $context, NodeInterface $left, NodeInterface $right)
    {
        return pow($left->evaluate($context), $right->evaluate($context));
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->add('pow(')
            ->compileNode($left)
            ->add(', ')
            ->compileNode($right)
            ->add(')');
    }
}