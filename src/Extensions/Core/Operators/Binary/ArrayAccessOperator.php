<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class ArrayAccessOperator extends BinaryOperator
{

    public function operators()
    {

    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }

    public function evaluate(EvaluationContext $context, Node $left, Node $right)
    {
        $left  = $left->evaluate($context);
        $right = $right->evaluate($context);

        return $left[ $right ];
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('$context->access(')
                 ->compileNode($left)
                 ->add(', ');

        $compiler->compileNode($right);

        $compiler->add(')');
    }
}