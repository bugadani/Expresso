<?php

namespace Expresso\Extensions\Core\Operators\Ternary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\ArrayAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\AndOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsSetOperator;

class ConditionalOperator extends TernaryOperator
{

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
        /** @var Node $left */
        /** @var Node $middle */
        /** @var Node $right */
        list($left, $middle, $right) = $node->getChildren();

        if (yield $left->evaluate($context)) {
            $result = (yield $middle->evaluate($context));
        } else {
            $result = (yield $right->evaluate($context));
        }

        yield $result;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $middle, $right) = $node->getChildren();

        $tempVar = $compiler->requestTempVariable();

        $leftOperand = (yield $compiler->compileNode($left));
        $compiler->pushContext();
        $compiler->add("if({$leftOperand}) {");

        $middleOperand = (yield $compiler->compileNode($middle));
        $compiler->compileStatements();
        $compiler->add("{$tempVar} = {$middleOperand};");

        $compiler->add(' } else {');

        $rightOperand = (yield $compiler->compileNode($right));
        $compiler->compileStatements();
        $compiler->add("{$tempVar} = {$rightOperand};");

        $compiler->add('}');

        $compiler->addStatement($compiler->popContext());
        $compiler->add($tempVar);
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
