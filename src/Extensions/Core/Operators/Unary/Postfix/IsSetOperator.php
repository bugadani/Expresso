<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\UnaryOperatorNode;

use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;


class IsSetOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is set';
    }

    public function createNode(CompilerConfiguration $config, $operand)
    {
        /** @var IdentifierNode $operand */
        return new UnaryOperatorNode($this, new DataNode($operand->getName()));
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
        $identifier = (yield $node->getChildAt(0)->evaluate($context));
        $context->setReturnValue($context->offsetExists($identifier));
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('$context->offsetExists(');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(')');
    }
}