<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class ModuloOperator extends BinaryOperator
{

    public function operators()
    {
        return 'mod';
    }

    public function evaluateSimple($left, $right)
    {
        if ($left < 0 && $right >= 0 || $left >= 0 && $right < 0) {
            return $right + $left % $right;
        } else {
            return $left % $right;
        }
    }

    public function compile(Compiler $compiler, Node $node)
    {
        //if(sign($left) != sign($right))

        $compiler->add('((');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(' < 0 && ');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(' > 0) || (');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(' < 0 && ');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(' > 0)');

        //then $right + $left % right
        $compiler->add(' ? (');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(' + ');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(' % ');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(')');

        //else $left % $right
        $compiler->add(' : (');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(' % ');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add('))');
    }
}