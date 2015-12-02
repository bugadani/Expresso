<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class LambdaOperator extends BinaryOperator
{

    public function operators()
    {
        return '->';
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        //intentionally empty
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        //intentionally empty
    }
}