<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Operators\BinaryOperator;

/**
 * Expression parser is based on the Shunting Yard algorithm by Edsger W. Dijkstra
 *
 * @link http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm
 */
class TokenStreamParser
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
     * @var TokenStream
     */
    private $tokens;

    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @var Parser
     */
    private $defaultParser;

    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    public function __construct(CompilerConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setDefaultParser(Parser $parser)
    {
        $this->defaultParser = $parser;
    }

    public function parseTokenStream(TokenStream $tokens)
    {
        $this->operatorStack = new \SplStack();
        $this->operandStack  = new \SplStack();
        $this->tokens        = $tokens;

        $tokens->next();
        $generator = $this->parse($this->defaultParser);

        $stack                 = new \SplStack();
        $beforeFirstYieldStack = new \SplStack();
        $stack->push($generator);
        $beforeFirstYieldStack->push(true);
        while (!$stack->isEmpty()) {
            /** @var \Generator $current */
            $current = $stack->top();
            if (!$beforeFirstYieldStack->top()) {
                $current->next();
            } else {
                $beforeFirstYieldStack->pop();
                $beforeFirstYieldStack->push(false);
            }

            if ($current->valid()) {
                $child = $current->current();
                if ($child !== null) {
                    $stack->push($child);
                    $beforeFirstYieldStack->push(true);
                }
            } else {
                $stack->pop();
                $beforeFirstYieldStack->pop();
            }
        }

        return $this->operandStack->pop();
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
    }

    public function addParser($name, Parser $parser)
    {
        $this->parsers[ $name ] = $parser;
    }

    public function hasParser($parser)
    {
        return isset($this->parsers[ $parser ]);
    }

    public function getParser($parser)
    {
        if (!isset($this->parsers[ $parser ])) {
            throw new \OutOfBoundsException("Parser not found: {$parser}");
        }

        return $this->parsers[ $parser ];
    }

    private function popOperator()
    {
        $operator = $this->operatorStack->pop();
        $right    = $this->operandStack->pop();
        if ($operator instanceof BinaryOperator) {
            $operatorNode = $operator->createNode($this->configuration, $this->operandStack->pop(), $right);
        } else {
            $operatorNode = $operator->createNode($this->configuration, $right);
        }
        $this->operandStack->push($operatorNode);
    }

    public function pushOperator(Operator $operator)
    {
        $this->popOperatorsWithHigherPrecedence($operator);
        $this->operatorStack->push($operator);
    }

    public function compareToStackTop(Operator $operator)
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

    public function popOperand()
    {
        return $this->operandStack->pop();
    }

    public function parse($parser)
    {
        if (!$parser instanceof Parser) {
            $parser = $this->getParser($parser);
        }

        return $parser->parse($this->tokens, $this);
    }

    /**
     * @param Operator $operator
     */
    public function popOperatorsWithHigherPrecedence(Operator $operator)
    {
        while ($this->compareToStackTop($operator)) {
            $this->popOperator();
        }
    }
}
