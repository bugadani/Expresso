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

    public function addParser($name, Parser $parser)
    {
        $this->parsers[ $name ] = $parser;
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
        $this->defaultParser->parse($this->tokens->current(), $this->tokens, $this);

        return $this->operandStack->pop();
    }

    public function pushOperatorSentinel()
    {
        $this->operatorStack->push(null);
    }

    public function popOperatorSentinel()
    {
        $this->operatorStack->pop();
    }

    public function hasParser($parser)
    {
        return isset($this->parsers[ $parser ]);
    }

    public function getParser($parser)
    {
        return $this->parsers[ $parser ];
    }

    public function popOperators()
    {
        while ($this->operatorStack->top() !== null) {
            $this->popOperator();
        }
    }

    public function popOperator()
    {
        $operator = $this->operatorStack->pop();
        $right    = $this->operandStack->pop();
        if ($operator instanceof BinaryOperator) {
            $operatorNode = $operator->createNode($this->operandStack->pop(), $right);
        } else {
            $operatorNode = $operator->createNode($right);
        }
        $this->operandStack->push($operatorNode);
    }

    public function pushOperator(Operator $operator)
    {
        $this->popOperatorCompared($operator);
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

    public function pushOperand(NodeInterface $node)
    {
        $this->operandStack->push($node);
    }

    public function popOperand()
    {
        return $this->operandStack->pop();
    }

    public function parse($parser)
    {
        $this->getParser($parser)->parse($this->tokens->current(), $this->tokens, $this);
    }

    public function topOperator()
    {
        return $this->operatorStack->top();
    }

    public function popOperatorStack()
    {
        return $this->operatorStack->pop();
    }

    /**
     * @param Operator $operator
     */
    public function popOperatorCompared(Operator $operator)
    {
        while ($this->compareToStackTop($operator)) {
            $this->popOperator();
        }
    }
}
