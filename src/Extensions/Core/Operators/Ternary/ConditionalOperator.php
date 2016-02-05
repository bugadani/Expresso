<?php

namespace Expresso\Extensions\Core\Operators\Ternary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
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
        )->setInline(true);
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var Node $left */
        /** @var Node $middle */
        /** @var Node $right */
        list($left, $middle, $right) = $node->getChildren();
        $condition = (yield $left->evaluate($context));
        $childNode = $condition ? $middle : $right;

        yield (yield $childNode->evaluate($context));
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $middle, $right) = $node->getChildren();

        $leftOperand   = (yield $compiler->compileNode($left, false));
        $middleOperand = (yield $compiler->compileNode($middle, false));
        $rightOperand  = (yield $compiler->compileNode($right, false));

        $compiler->add("(({$leftOperand}) ? ({$middleOperand}) : ($rightOperand))");
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