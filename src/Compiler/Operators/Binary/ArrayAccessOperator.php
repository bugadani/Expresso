<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\VariableAccessNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

class ArrayAccessOperator extends BinaryOperator
{

    public function operators()
    {

    }

    public function createNode($left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        $left  = $left->evaluate($context);
        $right = $right->evaluate($context);

        return $left[ $right ];
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->add('$context->access(')
                 ->compileNode($left)
                 ->add(', ');

        $compiler->compileNode($right);

        $compiler->add(')');
    }
}