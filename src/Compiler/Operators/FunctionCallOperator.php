<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

class FunctionCallOperator extends Operator
{
    /**
     * @var ExpressionFunction[]
     */
    private $functions;

    public function __construct($precedence, \ArrayObject $functions)
    {
        parent::__construct($precedence);
        $this->functions = $functions;
    }

    public function createNode($left)
    {
        if (!$left instanceof FunctionCallNode) {
            if ($left instanceof TernaryOperatorNode) {
                $left = new TernaryOperatorNode(
                    $left->getOperator(),
                    $left->getLeft(),
                    $left->getMiddle(),
                    new FunctionCallNode(
                        $left->getRight()
                    )
                );
            } else {
                $left = new FunctionCallNode($left);
            }
        }

        return $left;
    }

    public function operators()
    {

    }

    public function evaluate(EvaluationContext $context, Node $node, array $childResults)
    {

    }
}