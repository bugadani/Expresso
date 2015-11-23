<?php

namespace Expresso\Compiler\Operators\Ternary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\TernaryOperator;

class ConditionalOperator extends TernaryOperator
{

    public function operators()
    {

    }

    public function execute($left, $middle, $right)
    {
        return $left ? $middle : $right;
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $middle, NodeInterface $right)
    {
        $compiler->add('((')
            ->compileNode($left)
            ->add(') ? (')
            ->compileNode($middle)
            ->add(') : (')
            ->compileNode($right)
            ->add('))');
    }
}