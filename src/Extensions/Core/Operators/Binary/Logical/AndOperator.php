<?php

namespace Expresso\Extensions\Core\Operators\Binary\Logical;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class AndOperator extends BinaryOperator
{

    public function operators()
    {
        return '&&';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $right->addData('noEvaluate');

        return parent::createNode($config, $left, $right);
    }

    public function evaluate(EvaluationContext $context, Node $node, array $childResults)
    {
        //This implements short-circuit evaluation
        if ($childResults[0]) {
            $childNode = $node->getChildAt(1);
            $evaluator = new NodeTreeEvaluator();

            $childNode->removeData('noEvaluate');
            $result = $evaluator->evaluate($childNode, $context);
            $childNode->addData('noEvaluate');

            return $result;
        } else {
            return false;
        }
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add('&&')
                 ->compileNode($right)
                 ->add(')');
    }
}