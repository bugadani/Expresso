<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Operator;

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

    public function createNode($left, $right)
    {
        if ($left instanceof IdentifierNode) {
            $left = $this->functions[ $left->getName() ];
        }

        return new FunctionCallNode($left, $right);
    }

    public function operators()
    {

    }
}