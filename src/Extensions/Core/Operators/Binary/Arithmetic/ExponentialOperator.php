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
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(', ');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(')');
    }
}