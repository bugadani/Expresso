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
        list($left, $right) = $node->getChildren();

        $compiler->add('(');
        yield $compiler->compileNode($left);
        $compiler->add('%');
        yield $compiler->compileNode($right);
        $compiler->add(' === 0)');
    }
}