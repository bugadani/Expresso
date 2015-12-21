<?php

namespace Expresso\Extensions\Core\Operators\Binary\Test;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class DivisibleOperator extends BinaryOperator
{

    public function operators()
    {
        return 'is divisible by';
    }

    public function evaluateSimple($left, $right)
    {
        return $left % $right === 0;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('(');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add('%');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(' === 0)');
    }
}