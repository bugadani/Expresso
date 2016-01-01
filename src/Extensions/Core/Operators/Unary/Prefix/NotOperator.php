<?php

namespace Expresso\Extensions\Core\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class NotOperator extends UnaryOperator
{

    public function operators()
    {
        return '!';
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('!');
        yield $compiler->compileNode($node->getChildAt(0));
    }

    public function evaluateSimple($left)
    {
        return !$left;
    }
}