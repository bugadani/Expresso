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

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add('$context->offsetExists(')
                 ->add($compiledSource)
                 ->add(')');
    }
}