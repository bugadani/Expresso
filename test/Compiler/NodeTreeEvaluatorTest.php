<?php

namespace Expresso\Test\Compiler;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\AdditionOperator;

class NodeTreeEvaluatorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleExpression()
    {
        $nte = new NodeTreeEvaluator();

        $node = new BinaryOperatorNode(
            new AdditionOperator(0),
            new DataNode(1),
            new IdentifierNode('x')
        );

        $this->assertEquals(3, $nte->evaluate($node, new EvaluationContext(['x' => 2], new CompilerConfiguration())));
    }
}
