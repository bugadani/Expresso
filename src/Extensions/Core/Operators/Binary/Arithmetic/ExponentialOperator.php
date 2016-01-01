<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class ExponentialOperator extends BinaryOperator
{

    public function operators()
    {
        return '^';
    }

    public function evaluateSimple($left, $right)
    {
        return pow($left, $right);
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('pow(');
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(', ');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(')');
    }
}