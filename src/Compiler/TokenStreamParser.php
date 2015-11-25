<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Compiler\Operators\Ternary\ConditionalOperator;
use Expresso\Compiler\Operators\UnaryOperator;

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
     * @var OperatorCollection
     */
    private $binaryOperators;

    /**
     * @var OperatorCollection
     */
    private $prefixOperators;

    /**
     * @var OperatorCollection
     */
    private $postfixOperators;

    /**
     * @var ConditionalOperator
     */
    private $conditionalOperator;

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

    public function __construct(CompilerConfiguration $config)
    {
        $this->binaryOperators  = $config->getBinaryOperators();
        $this->prefixOperators  = $config->getUnaryPrefixOperators();
        $this->postfixOperators = $config->getUnaryPostfixOperators();
    }

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
        $this->parseExpression();

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

    public function popOperators()
    {
        while ($this->operatorStack->top() !== null) {
            $this->popOperator();
        }
    }

    private function parseArgumentList()
    {
        $arguments = [];

        if (!$this->tokens->nextTokenIf(Token::PUNCTUATION, ')')) {
            $token = $this->tokens->current();
            while (!$token->test(Token::PUNCTUATION, ')')) {
                $this->parseExpression();
                $arguments[] = $this->operandStack->pop();
                $token       = $this->tokens->expectCurrent(Token::PUNCTUATION, [',', ')']);
            }
        }

        return $arguments;
    }

    private function parsePostfixOperator()
    {
        $token = $this->tokens->next();
        if ($this->postfixOperators->isOperator($token->getValue())) {
            /** @var UnaryOperator $operator */
            $operator = $this->postfixOperators->getOperator($token->getValue());
            while ($this->compareToStackTop($operator)) {
                $this->popOperator();
            }

            $this->operandStack->push(
                $operator->createNode(
                    $this->operandStack->pop()
                )
            );
            $token = $this->tokens->next();
        }

        return $token;
    }

    private function parseArray()
    {
        $token = $this->tokens->current();
        $array = [];

        //iterate over tokens
        while (!$token->test(Token::PUNCTUATION, ']')) {
            //Checking here allows for a trailing comma.
            if ($this->tokens->nextTokenIf(Token::PUNCTUATION, ']')) {
                break;
            }
            //expressions are allowed as both array keys and values.
            $this->parseExpression();
            $value = $this->operandStack->pop();

            if ($this->tokens->current()->test(Token::PUNCTUATION, [':', '=>'])) {
                //the previous value was a key
                $key = $value;
                $this->parseExpression();
                $value         = $this->operandStack->pop();
                $array[ $key ] = $value;
            } else {
                $array[] = $value;
            }

            $token = $this->tokens->expectCurrent(Token::PUNCTUATION, [',', ']']);
        }
        //push array node to operand stack
        $this->operandStack->push(new DataNode($array));
    }

    private function parseToken()
    {
        do {
            $done  = true;
            $token = $this->tokens->next();

            $type  = $token->getType();
            $value = $token->getValue();

            try {
                $tokenParser = $this->parserCollection->getTokenParser($type);
                $tokenParser->parse($token, $this->tokens, $this);
                continue;
            } catch (\Exception $e) {

            }
            switch ($type) {
                case Token::PUNCTUATION:
                    //don't hardcode anything here - parentheses parsers?
                    switch ($value) {
                        case '(':
                            $this->parseExpression();
                            break;

                        case '[':
                            $this->parseArray();
                            break;

                        default:
                            $type = $token->getTypeString();
                            throw new SyntaxException("Unexpected {$type} ({$value}) token");
                    }
                    break;

                default:
                    $this->tokens->expectCurrent(
                        Token::OPERATOR,
                        [$this->prefixOperators, 'isOperator']
                    );
                    $this->pushOperator(
                        $this->prefixOperators->getOperator($value)
                    );
                    $done = false;
                    break;
            }
        } while (!$done);

        return $this->parsePostfixOperator();
    }

    public function parseExpression()
    {
        $this->defaultParser->parse($this->tokens->current(), $this->tokens, $this);
    }

    private function parseConditional()
    {
        //Only instantiate ConditionalOperator when there is a possibility of it being used
        if (!isset($this->conditionalOperator)) {
            $this->conditionalOperator = new ConditionalOperator(0);
        }

        $this->parseExpression();
        $this->tokens->expectCurrent(Token::PUNCTUATION, ':');
        $this->parseExpression();

        $right  = $this->operandStack->pop();
        $middle = $this->operandStack->pop();
        $left   = $this->operandStack->pop();

        $this->operandStack->push(
            $this->conditionalOperator->createNode($left, $middle, $right)
        );
    }

    private function popOperator()
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
        if ($operator === $top && $this->binaryOperators->exists($operator)) {
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
        $this->parsers[ $parser ]->parse($this->tokens->current(), $this->tokens, $this);
    }

    public function topOperator()
    {
        return $this->operatorStack->top();
    }

    public function popOperatorStack()
    {
        return $this->operatorStack->pop();
    }
}
