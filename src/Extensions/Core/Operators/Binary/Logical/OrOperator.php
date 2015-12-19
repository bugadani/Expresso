<?php

namespace Expresso\Extensions\Core\Operators\Binary\Logical;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class OrOperator extends BinaryOperator
{
    public function operators()
    {
        return '||';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $right->addData('noEvaluate');

        return parent::createNode($config, $left, $right);
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
        //This implements short-circuit evaluation
        yield $node->getChildAt(0)->evaluate($context);
        if (!$context->getReturnValue()) {
            $childNode = $node->getChildAt(1);
            yield $childNode->evaluate($context);
        } else {
            $context->setReturnValue(true);
        }
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add('||')
                 ->compileNode($right)
                 ->add(')');
    }
}