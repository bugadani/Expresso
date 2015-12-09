<?php

namespace Expresso\Extensions\Core\Operators\Ternary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\ArrayAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\AndOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsSetOperator;

class ConditionalOperator extends TernaryOperator
{

    public function operators()
    {

    }

    public function createNode(CompilerConfiguration $config, $left, $middle, $right)
    {
        $isSetOperator = $config->getOperatorByClass(IsSetOperator::class);
        $andOperator   = $config->getOperatorByClass(AndOperator::class);

        return parent::createNode(
            $config,
            $this->shouldCheckExistence($left) ?
                $andOperator->createNode(
                    $config,
                    $isSetOperator->createNode($config, $left),
                    $left
                ) : $left,
            $middle,
            $right
        );
    }


    public function evaluate(EvaluationContext $context, Node $left, Node $middle, Node $right)
    {
        return $left->evaluate($context) ? $middle->evaluate($context) : $right->evaluate($context);
    }

    public function compile(Compiler $compiler, Node $left, Node $middle, Node $right)
    {
        $compiler->add('((')
                 ->compileNode($left)
                 ->add(') ? (')
                 ->compileNode($middle)
                 ->add(') : (')
                 ->compileNode($right)
                 ->add('))');
    }

    /**
     * @param $left
     * @return bool
     */
    private function shouldCheckExistence($left)
    {
        if ($left instanceof IdentifierNode) {
            return true;
        } else if ($left instanceof OperatorNode) {
            return $left->isOperator(ArrayAccessOperator::class);
        } else {
            return false;
        }
    }
}