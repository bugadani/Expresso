<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Runtime\ExecutionContext;
use Expresso\Extensions\Core\Operators\Binary\ArrayAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\AndOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsSetOperator;

class ConditionalNode extends Node
{
    /**
     * @var Node
     */
    private $condition;

    /**
     * @var Node
     */
    private $onPositive;

    /**
     * @var Node
     */
    private $onNegative;

    public function __construct(CompilerConfiguration $config, Node $condition, Node $positive, Node $negative)
    {
        $isSetOperator = $config->getOperatorByClass(IsSetOperator::class);
        $andOperator   = $config->getOperatorByClass(AndOperator::class);

        $this->condition  = $this->shouldCheckExistence($condition)
            ? $andOperator->createNode(
                $config,
                $isSetOperator->createNode($config, $condition),
                $condition
            )
            : $condition;
        $this->onPositive = $positive;
        $this->onNegative = $negative;
    }

    /**
     * @param $left
     *
     * @return bool
     */
    private function shouldCheckExistence($left)
    {
        if ($left instanceof IdentifierNode || $left instanceof AccessNode) {
            return true;
        } else {
            return false;
        }
    }

    public function getChildren() : array
    {
        return [
            $this->condition,
            $this->onPositive,
            $this->onNegative
        ];
    }

    /**
     * Compile the given node.
     *
     * Note: this method should be executed by {@see Compiler).
     *
     * @param Compiler $compiler
     *
     * @return mixed
     */
    public function compile(Compiler $compiler)
    {
        $tempVar = $compiler->requestTempVariable();

        $compiledCondition = (yield $compiler->compileNode($this->condition));

        $compiler->pushContext();
        $compiler->add("if({$compiledCondition}) {");

        $middleOperand = (yield $compiler->compileNode($this->onPositive));
        $compiler->compileStatements();
        $compiler->add("{$tempVar} = {$middleOperand};");

        $compiler->add(' } else {');

        $rightOperand = (yield $compiler->compileNode($this->onNegative));
        $compiler->compileStatements();
        $compiler->add("{$tempVar} = {$rightOperand};");

        $compiler->add('}');

        $innerContext = $compiler->popContext();

        $compiler->addStatement($innerContext);
        $compiler->add($tempVar);
    }

    /**
     * Evaluate the given node.
     *
     * Note: this method should be executed with {@see GeneratorHelper).
     *
     * @param ExecutionContext $context
     * @return mixed
     */
    public function evaluate(ExecutionContext $context)
    {
        $leftValue = yield $this->condition->evaluate($context);
        if ($leftValue) {
            return yield $this->onPositive->evaluate($context);
        } else {
            return yield $this->onNegative->evaluate($context);
        }
    }
}