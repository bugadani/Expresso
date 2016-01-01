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
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(' < 0 && ');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(' > 0) || (');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(' < 0 && ');
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(' > 0)');

        //then $right + $left % right
        $compiler->add(' ? (');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(' + ');
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(' % ');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(')');

        //else $left % $right
        $compiler->add(' : (');
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(' % ');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add('))');
    }
}