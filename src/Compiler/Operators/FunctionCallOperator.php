<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
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

    public function createNode($left)
    {
        if (!$left instanceof FunctionCallNode) {

            if($left instanceof TernaryOperatorNode) {
                //TODO
            }

            $left = new FunctionCallNode($left);
        }

        return $left;
    }

    public function operators()
    {

    }
}