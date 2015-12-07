<?php

namespace Expresso\Extensions\Lambda\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class LambdaOperator extends BinaryOperator
{

    public function operators()
    {
        return '->';
    }

    public function execute(EvaluationContext $context, Node $left, Node $right)
    {
        //intentionally empty
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        //intentionally empty
    }
}