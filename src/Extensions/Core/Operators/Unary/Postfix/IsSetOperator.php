<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\Extensions\Core\Nodes\AccessNode;
use Expresso\Extensions\Core\Nodes\AssignableNode;
use Expresso\Runtime\ExecutionContext;

class IsSetOperator extends UnaryOperator
{

    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = $node->getOperand();
        if ($operand instanceof AssignableNode) {
            return $operand->evaluateContains($context);
        } else {
            return true;
        }
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = $node->getOperand();
        if ($operand instanceof AssignableNode) {
            yield $operand->compileContains($compiler);
        } else {
            $compiler->add('true');
        }
    }
}