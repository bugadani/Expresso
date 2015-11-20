<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Operators\BinaryOperator;

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
     * @var OperatorCollection
     */
    private $binaryOperators;

    /**
     * @var OperatorCollection
     */
    private $unaryPrefixOperators;

    /**
     * @var OperatorCollection
     */
    private $unaryPostfixOperators;

    /**
     * @var ConditionalOperator
     */
    private $conditionalOperator;

    /**
     * @var TokenStream
     */
    private $tokens;

    public function __construct(CompilerConfiguration $config)
    {
        $this->binaryOperators       = $config->getBinaryOperators();
        $this->unaryPrefixOperators  = $config->getUnaryPrefixOperators();
        $this->unaryPostfixOperators = $config->getUnaryPostfixOperators();
    }

    public function parse(TokenStream $tokens)
    {
        $this->operatorStack = new \SplStack();
        $this->operandStack  = new \SplStack();
        $this->tokens        = $tokens;

        $this->parseExpression();

        return $this->operandStack->pop();
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

    private function parseIdentifier($identifier)
    {
        if ($this->tokens->nextTokenIf(Token::PUNCTUATION, '(')) {
            //function call
            $node = new FunctionNode($identifier, $this->parseArgumentList());

            $lastOperator = $this->operatorStack->top();
            if ($lastOperator instanceof PropertyAccessOperator) {
                $this->operatorStack->pop();
                $node->setObject($this->operandStack->pop());
            }
        } else {
            $node = new IdentifierNode($identifier);
        }
        $this->operandStack->push($node);
    }

    private function parseVariable($variable)
    {
        $operand = new VariableNode($variable);
        while ($this->tokens->nextTokenIf(Token::PUNCTUATION, '[')) {
            //array indexing
            $this->parseExpression();
            $operand = new ArrayIndexNode($operand, $this->operandStack->pop());
        }
        $this->operandStack->push($operand);
    }

    private function parsePostfixOperator()
    {
        $token = $this->tokens->next();
        if ($token->test(Token::OPERATOR, [$this->unaryPostfixOperators, 'isOperator'])) {
            $operator = $this->unaryPostfixOperators->getOperator($token->getValue());
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
        $node  = new ArrayNode();
        $token = $this->tokens->current();

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
                $value = $this->operandStack->pop();
            } else {
                $key = null;
            }
            $node->add($value, $key);

            $token = $this->tokens->expectCurrent(Token::PUNCTUATION, [',', ']']);
        }
        //push array node to operand stack
        $this->operandStack->push($node);
    }

    private function parseToken()
    {
        do {
            $done  = true;
            $token = $this->tokens->next();

            $type  = $token->getType();
            $value = $token->getValue();

            switch ($type) {
                case Token::STRING:
                case Token::LITERAL:
                    $this->operandStack->push(new DataNode($value));
                    break;

                case Token::IDENTIFIER:
                    $this->parseIdentifier($value);
                    break;

                case Token::VARIABLE:
                    $this->parseVariable($value);
                    break;

                case Token::PUNCTUATION:
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
                        [$this->unaryPrefixOperators, 'isOperator']
                    );
                    $this->pushOperator(
                        $this->unaryPrefixOperators->getOperator($value)
                    );
                    $done = false;
                    break;
            }
        } while (!$done);

        return $this->parsePostfixOperator();
    }

    private function parseExpression()
    {
        //push sentinel
        $this->operatorStack->push(null);

        $token = $this->parseToken();

        while ($token->test(Token::OPERATOR, [$this->binaryOperators, 'isOperator'])) {
            $this->pushOperator(
                $this->binaryOperators->getOperator($token->getValue())
            );
            $token = $this->parseToken();
        }
        while ($this->operatorStack->top() !== null) {
            $this->popOperator();
        }
        //pop sentinel
        $this->operatorStack->pop();

        //A conditional is marked by '?' (punctuation, not operator) so it breaks the loop above.
        if ($token->test(Token::PUNCTUATION, '?')) {
            $this->parseConditional();
        }
    }

    private function parseConditional()
    {
        //Only instantiate ConditionalOperator when there is a possibility of it being used
        if (!isset($this->conditionalOperator)) {
            $this->conditionalOperator = new ConditionalOperator();
        }
        $left = $this->operandStack->pop();

        // Check whether the current expression is a simplified conditional
        // expression (expr1 ?: expr3)
        if (!$this->tokens->nextTokenIf(Token::PUNCTUATION, ':')) {
            $this->parseExpression();
            $middle = $this->operandStack->pop();
            $this->tokens->expectCurrent(Token::PUNCTUATION, ':');
        } else {
            $middle = null;
        }

        $this->parseExpression();
        $right = $this->operandStack->pop();

        $this->operandStack->push(
            $this->conditionalOperator->createNode($left, $right, $middle)
        );
    }

    private function popOperator()
    {
        $operator = $this->operatorStack->pop();
        $right    = $this->operandStack->pop();
        if ($operator instanceof BinaryOperator) {
            $operatorNode = $operator->createNode(
                $this->operandStack->pop(),
                $right
            );
        } else {
            $operatorNode = $operator->createNode(
                null,
                $right
            );
        }
        $this->operandStack->push($operatorNode);
    }

    private function pushOperator(Operator $operator)
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
}
