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

    public function evaluate(EvaluationContext $context, Node $node)
    {
        $condition = (yield $node->getChildAt(0)->evaluate($context));
        $childNode = $node->getChildAt($condition ? 1 : 2);

        yield $childNode->evaluate($context);
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('((');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(') ? (');
        yield $node->getChildAt(1)->compile($compiler);
        $compiler->add(') : (');
        yield $node->getChildAt(2)->compile($compiler);
        $compiler->add('))');
    }

    /**
     * @param $left
     *
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