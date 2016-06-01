<?php

namespace Expresso\Extensions\Core\Parsers;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operator;
use Expresso\Compiler\Operators\BinaryOperator;

/**
 * Expression parser is based on the Shunting Yard algorithm by Edsger W. Dijkstra
 *
 * @link http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm
 */
class OperatorParser
{
    /**
     * @var \SplStack
     */
    private $operatorStack;

    /**
     * @var \SplStack
     */
    private $operandStack;

    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    public function __construct(CompilerConfiguration $configuration)
    {
        $this->configuration = $configuration;

        $this->operatorStack = new \SplStack();
        $this->operandStack  = new \SplStack();
    }

    public function pushOperatorSentinel()
    {
        $this->operatorStack->push(null);
    }

    public function popOperatorSentinel() : Node
    {
        while ($this->operatorStack->top() !== null) {
            $this->popOperator();
        }
        $this->operatorStack->pop();

        return $this->operandStack->pop();
    }

    private function popOperator()
    {
        /** @var Operator $operator */
        $operator = $this->operatorStack->pop();

        $operands = [];
        for ($i = 0; $i < $operator->getOperandCount(); $i++) {
            $operands[] = $this->operandStack->pop();
        }
        $operands = array_reverse($operands);

        $operatorNode = $operator->createNode($this->configuration, ...$operands);
        $this->operandStack->push($operatorNode);
    }

    public function pushOperator(Operator $operator)
    {
        while ($this->compareToStackTop($operator)) {
            $this->popOperator();
        }
        $this->operatorStack->push($operator);
    }

    private function compareToStackTop(Operator $operator) : bool
    {
        $top = $this->operatorStack->top();
        if ($top === null) {
            return false;
        }
        if ($operator === $top && $operator instanceof BinaryOperator) {
            switch ($operator->getAssociativity()) {
                case Operator::LEFT:
                    return true;
                case Operator::RIGHT:
                    return false;
                default:
                    //e.g. (5 is divisible by 2 is divisible by 3) is not considered valid
                    $operatorClass = get_class($operator);
                    throw new ParseException("Binary operator '{$operatorClass}' is not associative");
            }
        }

        return $top->getPrecedence() >= $operator->getPrecedence();
    }

    public function pushOperand(Node $node)
    {
        $this->operandStack->push($node);
    }
}
