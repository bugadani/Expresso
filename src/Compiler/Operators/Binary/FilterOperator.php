<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\ExecutionContext;

class FilterOperator extends BinaryOperator
{

    public function execute(ExecutionContext $context, NodeInterface $left, NodeInterface $right)
    {
        // TODO: Implement execute() method.
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        // TODO: Implement compile() method.
    }

    public function operators()
    {
        return '|';
    }
}