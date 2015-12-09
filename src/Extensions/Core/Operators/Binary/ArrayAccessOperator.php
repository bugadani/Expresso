<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;

class ArrayAccessOperator extends BinaryOperator
{

    public function operators()
    {

    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }

    public function evaluateSimple($left, $right)
    {
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