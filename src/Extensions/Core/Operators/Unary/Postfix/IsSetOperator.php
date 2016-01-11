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
        return parent::createNode($config, new DataNode($operand->getName()));
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        yield $context->offsetExists($node->getOperand()->getValue());
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $compiledOperand = (yield $compiler->compileNode($node->getOperand()));

        if ($node->isInline()) {
            $compiledSource  = $compiledOperand->source;
        } else {
            $compiledSource  = $compiler->addTempVariable($compiledOperand);
        }

        $compiler->add('$context->offsetExists(');
        $compiler->add($compiledSource);
        $compiler->add(')');
    }
}