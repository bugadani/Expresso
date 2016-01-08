<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\Compiler\ParserSequence\Container;
use Expresso\Compiler\Utils\GeneratorHelper;

/**
 * Expression parser is based on the Shunting Yard algorithm by Edsger W. Dijkstra
 *
 * @link http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm
 */
class Parser
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
     * @var Container
     */
    private $parsers;

    /**
     * @var string
     */
    private $defaultParserName;

    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    public function __construct(CompilerConfiguration $configuration)
    {
        $this->configuration = $configuration;
        $this->parsers       = new Container();
    }

    public function setDefaultParserName($parserName)
    {
        $this->defaultParserName = $parserName;
    }

    public function parseTokenStream(TokenStream $tokens)
    {
        $this->operatorStack = new \SplStack();
        $this->operandStack  = new \SplStack();

        $generator = $this->parsers->get($this->defaultParserName)->parse($tokens);

        return GeneratorHelper::executeGeneratorsRecursive($generator);
    }

    public function pushOperatorSentinel()
    {
        $this->operatorStack->push(null);
    }

    public function popOperatorSentinel()
    {
        while ($this->operatorStack->top() !== null) {
            $this->popOperator();
        }
        $this->operatorStack->pop();

        return $this->operandStack->pop();
    }

    private function popOperator()
    {
        $operator = $this->operatorStack->pop();
        if ($operator instanceof TernaryOperator) {
            $right        = $this->operandStack->pop();
            $middle       = $this->operandStack->pop();
            $left         = $this->operandStack->pop();
            $operatorNode = $operator->createNode($this->configuration, $left, $middle, $right);
        } else if ($operator instanceof BinaryOperator) {
            $right        = $this->operandStack->pop();
            $left         = $this->operandStack->pop();
            $operatorNode = $operator->createNode($this->configuration, $left, $right);
        } else {
            $right        = $this->operandStack->pop();
            $operatorNode = $operator->createNode($this->configuration, $right);
        }
        $this->operandStack->push($operatorNode);
    }

    public function pushOperator(Operator $operator)
    {
        while ($this->compareToStackTop($operator)) {
            $this->popOperator();
        }
        $this->operatorStack->push($operator);
    }

    private function compareToStackTop(Operator $operator)
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
                    $symbols = $operator->operators();
                    if (is_array($symbols)) {
                        $symbols = implode(', ', $symbols);
                    }
                    throw new ParseException("Binary operator '{$symbols}' is not associative");
            }
        }

        return $top->getPrecedence() >= $operator->getPrecedence();
    }

    public function pushOperand(Node $node)
    {
        $this->operandStack->push($node);
    }

    /**
     * @return Container
     */
    public function getParserContainer()
    {
        return $this->parsers;
    }
}
