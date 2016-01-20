<?php

namespace Expresso\Extensions\Core;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\ArgumentListNode;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Compiler\Nodes\StringNode;
use Expresso\Compiler\Operator;
use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Operators\FunctionCallOperator;
use Expresso\Compiler\Parser\OperatorParser;
use Expresso\Compiler\Parser\Parsers\ParserReference;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Extension;
use Expresso\Extensions\Core\Nodes\ListDataNode;
use Expresso\Extensions\Core\Nodes\MapDataNode;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\AdditionOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\DivisionOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\ExponentialOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\IntegerDivisionOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\ModuloOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\MultiplicationOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\RemainderOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\SubtractionOperator;
use Expresso\Extensions\Core\Operators\Binary\ArrayAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\Bitwise\BitwiseAndOperator;
use Expresso\Extensions\Core\Operators\Binary\Bitwise\BitwiseOrOperator;
use Expresso\Extensions\Core\Operators\Binary\Bitwise\BitwiseXorOperator;
use Expresso\Extensions\Core\Operators\Binary\Bitwise\LeftArithmeticShiftOperator;
use Expresso\Extensions\Core\Operators\Binary\Bitwise\RightArithmeticShiftOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\EqualsOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\GreaterThanOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\GreaterThanOrEqualsOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\IdenticalOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\LessThanOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\LessThanOrEqualsOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\NotEqualsOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\NotIdenticalOperator;
use Expresso\Extensions\Core\Operators\Binary\ConditionalOperator as BinaryConditionalOperator;
use Expresso\Extensions\Core\Operators\Binary\FilterOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\AndOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\OrOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\XorOperator;
use Expresso\Extensions\Core\Operators\Binary\NullSafeAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\RangeOperator;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\Strings\ConcatenationOperator;
use Expresso\Extensions\Core\Operators\Binary\Test\DivisibleOperator;
use Expresso\Extensions\Core\Operators\Binary\Test\NotDivisibleOperator;
use Expresso\Extensions\Core\Operators\Ternary\ConditionalOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\EvenOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\InfiniteRangeOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsNotSetOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsSetOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\OddOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\BitwiseNotOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\MinusOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class Core extends Extension
{
    public function getBinaryOperators()
    {
        return [
            //comparison
            new EqualsOperator(7),
            new IdenticalOperator(7),
            new NotIdenticalOperator(7),
            new NotEqualsOperator(7),
            //logical
            new AndOperator(3),
            new OrOperator(2),
            new XorOperator(1),
            //bitwise
            new BitwiseAndOperator(6),
            new BitwiseOrOperator(4),
            new BitwiseXorOperator(5),
            new LeftArithmeticShiftOperator(9),
            new RightArithmeticShiftOperator(9),
            //arithmetic operators
            new AdditionOperator(10),
            new SubtractionOperator(10),
            new MultiplicationOperator(11),
            new DivisionOperator(11),
            new IntegerDivisionOperator(11),
            new RemainderOperator(11),
            new ModuloOperator(11),
            new ExponentialOperator(14, Operator::RIGHT),
            new DivisibleOperator(8, Operator::NONE),
            new NotDivisibleOperator(8, Operator::NONE),
            //comparison
            new LessThanOperator(8),
            new LessThanOrEqualsOperator(8),
            new GreaterThanOperator(8),
            new GreaterThanOrEqualsOperator(8),
            //test
            /*
            new ContainsOperator(8, Operator::NONE),
            new NotContainsOperator(8, Operator::NONE),*/
            //other
            new ArrayAccessOperator(17),
            new FunctionCallOperator(12),
            new BinaryConditionalOperator(1),
            new ConcatenationOperator(10),
            new SimpleAccessOperator(16),
            new NullSafeAccessOperator(16),
            new FilterOperator(11),
            new RangeOperator(12)
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            /*new PreDecrementOperator(13, Operator::RIGHT),
            new PreIncrementOperator(13, Operator::RIGHT)*/
            new NotOperator(12, Operator::RIGHT),
            new BitwiseNotOperator(13, Operator::RIGHT),
            new MinusOperator(13, Operator::RIGHT)
        ];
    }

    public function getPostfixUnaryOperators()
    {
        return [
            new IsSetOperator(15, Operator::RIGHT),
            new IsNotSetOperator(15, Operator::RIGHT),
            new EvenOperator(15, Operator::NONE),
            new OddOperator(15, Operator::NONE),
            /* new PostDecrementOperator(15),
             new PostIncrementOperator(15),
             new EmptyOperator(15),
             new NotEmptyOperator(15)*/
            new InfiniteRangeOperator(15, Operator::NONE)
        ];
    }

    public function getTernaryOperators()
    {
        return [
            new ConditionalOperator(0)
        ];
    }

    public function getSymbols()
    {
        return [',', '[', ']', '(', ')', '{', '}', ':', '?', '\\', '=>'];
    }

    public function addParsers(OperatorParser $parser, CompilerConfiguration $configuration)
    {
        $parserContainer = $parser->getParserContainer();

        $conditionalOperator  = $configuration->getOperatorByClass(ConditionalOperator::class);
        $functionCallOperator = $configuration->getOperatorByClass(FunctionCallOperator::class);
        $arrayAccessOperator  = $configuration->getOperatorByClass(ArrayAccessOperator::class);

        $returnNode = function ($className) {
            return function (Token $token) use ($className) {
                return new $className($token->getValue());
            };
        };

        $returnArgument = function ($argument) {
            return function (array $children) use ($argument) {
                return $children[ $argument ];
            };
        };

        $prefixOperators  = $configuration->getPrefixOperators();
        $binaryOperators  = $configuration->getBinaryOperators();
        $postfixOperators = $configuration->getUnaryOperators();

        $pushOperator = function (OperatorCollection $operators) use ($parser) {
            return function (Token $token) use ($parser, $operators) {
                $tokenValue = $token->getValue();
                $operator   = $operators->getOperator($tokenValue);
                $parser->pushOperator($operator);
            };
        };

        //Primitive parsers
        $comma        = TokenParser::create(Token::SYMBOL, ',');
        $semicolon    = TokenParser::create(Token::SYMBOL, ':');
        $questionMark = TokenParser::create(Token::SYMBOL, '?');

        $openingSquareBracket  = TokenParser::create(Token::SYMBOL, '[');
        $closingSquareBrackets = TokenParser::create(Token::SYMBOL, ']');

        $openingParenthesis = TokenParser::create(Token::SYMBOL, '(');
        $closingParenthesis = TokenParser::create(Token::SYMBOL, ')');

        $prefixParser  = TokenParser::create(Token::OPERATOR, [$prefixOperators, 'isOperator'])
                                    ->process($pushOperator($prefixOperators));
        $binaryParser  = TokenParser::create(Token::OPERATOR, [$binaryOperators, 'isOperator'])
                                    ->process($pushOperator($binaryOperators));
        $postfixParser = TokenParser::create(Token::OPERATOR, [$postfixOperators, 'isOperator'])
                                    ->process($pushOperator($postfixOperators));

        $identifier      = TokenParser::create(Token::IDENTIFIER);
        $constantData    = TokenParser::create(Token::CONSTANT);
        $constantString  = TokenParser::create(Token::STRING);
        $endOfExpression = TokenParser::create(Token::EOF);

        $expression = new ParserReference($parserContainer, 'expression');
        $operand    = new ParserReference($parserContainer, 'operand');

        //Other parsers
        $mapParser = function ($separatorSymbol) use ($expression, $comma) {
            $separator = TokenParser::create(Token::SYMBOL, $separatorSymbol);

            return $separator
                ->followedBy($expression)
                ->followedBy(
                    $comma
                        ->followedBy($expression)
                        ->followedBy($separator)
                        ->followedBy($expression)
                        ->process(
                            function (array $children) {
                                return [$children[1], $children[3]];
                            }
                        )
                        ->repeated()
                        ->optional()
                );
        };

        $expressionListSeparatedByComma = $expression
            ->repeatSeparatedBy($comma);

        $listParser = $comma
            ->followedBy($expressionListSeparatedByComma)
            ->process($returnArgument(1));

        $arrayElementList = $expression
            ->followedBy(
                $listParser
                    ->orA($mapParser(':'))
                    ->orA($mapParser('=>'))
                    ->optional()
            );

        $arrayDefinition = $openingSquareBracket
            ->followedBy($arrayElementList->optional())
            ->followedBy($closingSquareBrackets);

        $functionCall = $openingParenthesis
            ->followedBy($expressionListSeparatedByComma->optional())
            ->followedBy($closingParenthesis);

        $arrayAccess = $openingSquareBracket
            ->followedBy($expression)
            ->followedBy($closingSquareBrackets);

        $prefixOperatorSequence = $prefixParser->repeated();

        $dereferenceSequence = $functionCall
            ->orA($arrayAccess)
            ->repeated();

        $postfixOperatorSequence = $postfixParser
            ->repeated()
            ->optional();

        $groupedExpression = $openingParenthesis
            ->followedBy($expression)
            ->followedBy($closingParenthesis)
            ->process($returnArgument(1));

        $operandParser = $identifier
            ->orA($constantData)
            ->orA($constantString)
            ->orA($arrayDefinition)
            ->orA($groupedExpression);

        $term = $prefixOperatorSequence->optional()
                                       ->followedBy($operand)
                                       ->followedBy($dereferenceSequence->optional())
                                       ->followedBy($postfixOperatorSequence->optional());

        $binaryExpression = $term->repeatSeparatedBy($binaryParser);

        $conditionalExpressionSuffix = $questionMark
            ->followedBy($expression)
            ->followedBy($semicolon)
            ->followedBy($expression);

        $expressionParser = $binaryExpression
            ->followedBy($conditionalExpressionSuffix->optional());

        $program = $expression
            ->followedBy($endOfExpression)
            ->process($returnArgument(0));

        //Set processor functions
        $identifier->process($returnNode(IdentifierNode::class));
        $constantData->process($returnNode(DataNode::class));
        $constantString->process($returnNode(StringNode::class));
        $operandParser->process([$parser, 'pushOperand']);

        $arrayDefinition->process(
            function (array $children) {
                $items = $children[1];

                if ($items === null) {
                    return new ListDataNode();
                }

                list($first, $array) = $items;
                if (empty($array)) {

                    if ($first instanceof OperatorNode) {
                        $isRangeOperator = $first->isOperator(RangeOperator::class)
                                           || $first->isOperator(InfiniteRangeOperator::class);

                        if ($isRangeOperator) {
                            return $first;
                        }
                    }
                    $isMap = false;
                } else {
                    $isMap = ($array[0] instanceof Token
                              && $array[0]->test(Token::SYMBOL, [':', '=>']));
                }

                if ($isMap) {
                    $node = new MapDataNode();
                    $node->add($first, $array[1]);

                    /** @var Node $key */
                    /** @var Node $value */
                    if (!empty($array[2])) {
                        foreach ($array[2] as list($key, $value)) {
                            $node->add($key, $value);
                        }
                    }
                } else {
                    $node = new ListDataNode();
                    $node->add($first);
                    if (!empty($array)) {
                        foreach ($array as $item) {
                            $node->add($item);
                        }
                    }
                }

                return $node;
            }
        );

        $functionCall->process(
            function (array $children) use ($parser, $functionCallOperator) {
                $parser->pushOperator($functionCallOperator);

                $arguments = new ArgumentListNode();
                if (!empty($children[1])) {
                    foreach ($children[1] as $argument) {
                        $arguments->add($argument);
                    }
                }
                $parser->pushOperand($arguments);
            }
        );

        $arrayAccess->process(
            function (array $children) use ($parser, $arrayAccessOperator) {
                $parser->pushOperator($arrayAccessOperator);
                $parser->pushOperand($children[1]);
            }
        );

        $conditionalExpressionSuffix->process(
            function (array $children) {
                return [$children[1], $children[3]];
            }
        );

        $expressionParser
            ->runBefore([$parser, 'pushOperatorSentinel'])
            ->process(
                function (array $children) use ($parser, $conditionalOperator) {

                    if ($children[1] !== null) {
                        list($middle, $right) = $children[1];

                        $parser->pushOperator($conditionalOperator);

                        $parser->pushOperand($middle);
                        $parser->pushOperand($right);
                    }

                    return $parser->popOperatorSentinel();
                }
            );

        $parserContainer->set('operand', $operandParser);
        $parserContainer->set('expression', $expressionParser);
        $parserContainer->set('program', $program);

        $parser->setDefaultParserName('program');
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction('count', __NAMESPACE__ . '\expression_function_count'),
            new ExpressionFunction('join', __NAMESPACE__ . '\expression_function_join'),
            new ExpressionFunction('skip', __NAMESPACE__ . '\expression_function_skip'),
            new ExpressionFunction('popcount', __NAMESPACE__ . '\expression_function_population_count'),
            new ExpressionFunction('replace', __NAMESPACE__ . '\expression_function_replace'),
            new ExpressionFunction('reverse', 'strrev'),
            new ExpressionFunction('take', __NAMESPACE__ . '\expression_function_take'),
        ];
    }
}

/**
 * @param int      $start
 * @param int|null $end
 *
 * @return \Generator
 */
function range($start, $end = null)
{
    if ($end === null) {
        while (true) {
            yield $start++;
        }
    } else {
        for ($num = $start; $num <= $end; $num++) {
            yield $num;
        }
    }
}

function expression_function_count($data)
{
    if (is_array($data)) {
        return count($data);
    } else if ($data instanceof \Iterator) {
        return iterator_count($data);
    } else {
        throw new \InvalidArgumentException('Collection must be an array or an Iterator');
    }
}

function expression_function_replace($string, $search, $replacement = null)
{
    if ($replacement === null) {
        if (!is_array($search)) {
            throw new \InvalidArgumentException(
                '$search must be an array if only two arguments are supplied to replace'
            );
        }

        return str_replace(array_keys($search), $search, $string);
    } else {
        return str_replace($search, $replacement, $string);
    }
}

function expression_function_join($collection, $glue = '')
{
    if ($collection instanceof \Iterator) {
        $collection = iterator_to_array($collection);
    }
    if (is_array($collection)) {
        return implode($glue, $collection);
    } else {
        throw new \InvalidArgumentException('Collection must be an array or an Iterator');
    }
}

function expression_function_take($collection, $number)
{
    if (is_array($collection)) {
        return array_slice($collection, 0, $number, true);
    } else if ($collection instanceof \Iterator) {
        return new \LimitIterator($collection, 0, $number);
    } else {
        throw new \InvalidArgumentException('Collection must be an array or an Iterator');
    }
}

function expression_function_skip($collection, $number)
{
    if (is_array($collection)) {
        return array_slice($collection, $number, null, true);
    } else if ($collection instanceof \Iterator) {
        return new \LimitIterator($collection, $number);
    } else {
        throw new \InvalidArgumentException('Collection must be an array or an Iterator');
    }
}