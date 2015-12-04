<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Nodes\VariableAccessNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Compiler\Operators\Ternary\ConditionalOperator;
use Expresso\EvaluationContext;

class NullSafeAccessOperator extends BinaryOperator
{

    public function operators()
    {
        return '?.';
    }

    public function createNode($left, $right)
    {
        return new TernaryOperatorNode(
            new ConditionalOperator(1),
            new BinaryOperatorNode(
                new IdenticalOperator(1),
                $left,
                new DataNode(null)
            ),
            new DataNode(null),
            new VariableAccessNode(new SimpleAccessOperator(0), $left, $right)
        );
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
    }
}