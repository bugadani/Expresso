<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Compiler\Operators\Ternary\ConditionalOperator;
use Expresso\Compiler\Operators\Unary\Postfix\IsNotSetOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Logical\Operators\Binary\OrOperator;

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
                new OrOperator(1),
                $left instanceof IdentifierNode ? new UnaryOperatorNode(new IsNotSetOperator(1), $left) : $left,
                new BinaryOperatorNode(
                    new IdenticalOperator(1),
                    $left,
                    new DataNode(null)
                )
            ),
            new DataNode(null),
            new BinaryOperatorNode(new SimpleAccessOperator(0), $left, $right)
        );
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
    }
}