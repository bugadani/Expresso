<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

class ModuloOperator extends BinaryOperator
{

    public function operators()
    {
        return 'mod';
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        $left  = $left->evaluate($context);
        $right = $right->evaluate($context);

        if ($left < 0 && $right >= 0 || $left >= 0 && $right < 0) {
            return $right + $left % $right;
        } else {
            return $left % $right;
        }
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        //if(sign($left) != sign($right))
        $compiler
            ->add('((')
            ->compileNode($left)
            ->add(' < 0 && ')
            ->compileNode($right)
            ->add(' > 0) || (')
            ->compileNode($right)
            ->add(' < 0 && ')
            ->compileNode($left)
            ->add(' > 0)');

        //then $right + $left % right
        $compiler
            ->add(' ? (')
            ->compileNode($right)
            ->add(' + ')
            ->compileNode($left)
            ->add(' % ')
            ->compileNode($right)
            ->add(')');

        //else $left % $right
        $compiler
            ->add(': (')
            ->compileNode($left)
            ->add(' % ')
            ->compileNode($right)
            ->add('))');
    }
}