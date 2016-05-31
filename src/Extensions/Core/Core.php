<?php

namespace Expresso\Extensions\Core;

use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Parser\GrammarParser;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\ArrayAccessNode;
use Expresso\Runtime\RuntimeFunction;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Extensions\Core\Nodes\StatementNode;
use Expresso\Extensions\Core\Nodes\StringNode;
use Expresso\Compiler\Operator;
use Expresso\Compiler\OperatorCollection;
use Expresso\Extensions\Core\Operators\Binary\FunctionCallOperator;
use Expresso\Extensions\Core\Operators\Binary\AssignmentOperator;
use Expresso\Extensions\Core\Parsers\OperatorParser;
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
use Expresso\Extensions\Core\Operators\Ternary\TernaryConditionalOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\EvenOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\InfiniteRangeOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsNotSetOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsSetOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\OddOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\BitwiseNotOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\MinusOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

/**
 * Class Core defines the core language elements, including operators and some basic functions.
 *
 * @package Expresso\Extensions\Core
 */
class Core extends Extension
{
    /**
     * @inheritdoc
     */
    public function getBinaryOperators() : array
    {
        return [
            //comparison
            '='                   => new EqualsOperator(7),
            '=='                  => new IdenticalOperator(7),
            '!=='                 => new NotIdenticalOperator(7),
            '!='                  => new NotEqualsOperator(7),
            //logical
            '&&'                  => new AndOperator(3),
            '||'                  => new OrOperator(2),
            'xor'                 => new XorOperator(1),
            //bitwise
            'b-and'               => new BitwiseAndOperator(6),
            'b-or'                => new BitwiseOrOperator(4),
            'b-xor'               => new BitwiseXorOperator(5),
            '<<'                  => new LeftArithmeticShiftOperator(9),
            '>>'                  => new RightArithmeticShiftOperator(9),
            //arithmetic operators
            '+'                   => new AdditionOperator(10),
            '-'                   => new SubtractionOperator(10),
            '*'                   => new MultiplicationOperator(11),
            '/'                   => new DivisionOperator(11),
            'div'                 => new IntegerDivisionOperator(11),
            '%'                   => new RemainderOperator(11),
            'mod'                 => new ModuloOperator(11),
            '^'                   => new ExponentialOperator(14, Operator::RIGHT),
            'is divisible by'     => new DivisibleOperator(8, Operator::NONE),
            'is not divisible by' => new NotDivisibleOperator(8, Operator::NONE),
            //comparison
            '<'                   => new LessThanOperator(8),
            '<='                  => new LessThanOrEqualsOperator(8),
            '>'                   => new GreaterThanOperator(8),
            '>='                  => new GreaterThanOrEqualsOperator(8),
            //test
            /*
          'in' => new ContainsOperator(8, Operator::NONE),
          'not in' => new NotContainsOperator(8, Operator::NONE),*/
            //other
            new ArrayAccessOperator(17),
            new FunctionCallOperator(17),
            '?:'                  => new BinaryConditionalOperator(1),
            '~'                   => new ConcatenationOperator(10),
            '.'                   => new SimpleAccessOperator(17),
            '?.'                  => new NullSafeAccessOperator(18),
            '|'                   => new FilterOperator(11),
            '..'                  => new RangeOperator(12),//todo csak []-ban
            ':='                  => new AssignmentOperator(-1)
        ];
    }

    /**
     * @inheritdoc
     */
    public function getPrefixUnaryOperators() : array
    {
        return [
            /*'--'=>new PreDecrementOperator(13, Operator::RIGHT),
           '++' =>new PreIncrementOperator(13, Operator::RIGHT)*/
            '!' => new NotOperator(12, Operator::RIGHT),
            '~' => new BitwiseNotOperator(13, Operator::RIGHT),
            '-' => new MinusOperator(13, Operator::RIGHT)
        ];
    }

    /**
     * @inheritdoc
     */
    public function getPostfixUnaryOperators() : array
    {
        return [
            'is set'     => new IsSetOperator(15, Operator::RIGHT),
            'is not set' => new IsNotSetOperator(15, Operator::RIGHT),
            'is even'    => new EvenOperator(15, Operator::NONE),
            'is odd'     => new OddOperator(15, Operator::NONE),
            /*'--' =>new PostDecrementOperator(15),
           '++' => new PostIncrementOperator(15),
           'is empty' => new EmptyOperator(15),
           'is not empty' => new NotEmptyOperator(15)*/
            '...'        => new InfiniteRangeOperator(15, Operator::NONE)//todo csak []-ban
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTernaryOperators() : array
    {
        return [
            new TernaryConditionalOperator(0)
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSymbols() : array
    {
        return [',', '[', ']', '(', ')', '{', '}', ':', '?', '\\', '=>', ';'];
    }

    /**
     * @inheritdoc
     */
    public function addParsers(GrammarParser $grammarParser, CompilerConfiguration $configuration)
    {
        $parserContainer = $grammarParser->getContainer();
        $parser          = new OperatorParser($configuration);

        $conditionalOperator  = $configuration->getOperatorByClass(TernaryConditionalOperator::class);
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
        $colon        = TokenParser::create(Token::SYMBOL, ':');
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

        //TODO: block: {expressionList}
        $expressionListSeparatedByComma = $expression
            ->repeatSeparatedBy($comma);

        $listParser = $comma
            ->followedBy($expressionListSeparatedByComma)
            ->process($returnArgument(1));

        //TODO: process based on what matched - optional to override parent processor function
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

        $argumentList = $questionMark
            ->orA($expression)
            ->repeatSeparatedBy($comma);

        $functionCall = $openingParenthesis
            ->followedBy($argumentList->optional())
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

        $expressions = $expression
            ->repeated()
            ->process(
                function (array $children) {
                    if (count($children) > 1) {
                        return new StatementNode($children);
                    } else {
                        return $children[0];
                    }
                }
            );

        $groupedExpression = $openingParenthesis
            ->followedBy($expressions)
            ->followedBy($closingParenthesis)
            ->process($returnArgument(1));

        $operandParser = $identifier
            ->orA($constantData)
            ->orA($constantString)
            ->orA($arrayDefinition)
            ->orA($groupedExpression);

        $term = $prefixOperatorSequence->optional()
                                       ->followedBy(
                                           $operand->followedBy($dereferenceSequence->optional())
                                       )
                                       ->followedBy($postfixOperatorSequence->optional());

        $binaryExpression = $term->repeatSeparatedBy($binaryParser);

        $conditionalExpressionSuffix = $questionMark
            ->followedBy($expression)
            ->followedBy($colon)
            ->followedBy($expression);

        $expressionParser = $binaryExpression
            ->followedBy($conditionalExpressionSuffix->optional());

        $program = $expressions
            ->followedBy($endOfExpression)
            ->process($returnArgument(0));

        //Set processor functions
        $identifier->process($returnNode(IdentifierNode::class));
        $constantData->process($returnNode(DataNode::class));
        $constantString->process($returnNode(StringNode::class));
        $operandParser->process(function ($node) use ($parser) {
            $parser->pushOperand($node);

            return $node;
        });

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
                        if ($argument instanceof Token && $argument->getValue() === '?') {
                            $arguments->addPlaceholderArgument();
                        } else {
                            $arguments->add($argument);
                        }
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

        //TODO: program: expr [; expr]...
        $parserContainer->set('program', $program);

        $grammarParser->setSentence('program');
    }

    /**
     * @inheritdoc
     */
    public function getFunctions() : array
    {
        return [
            'count'   => RuntimeFunction::new(__NAMESPACE__ . '\expression_function_count', 1),
            'join'    => RuntimeFunction::new(__NAMESPACE__ . '\expression_function_join', 1, 2),
            'skip'    => RuntimeFunction::new(__NAMESPACE__ . '\expression_function_skip', 2),
            'replace' => RuntimeFunction::new(__NAMESPACE__ . '\expression_function_replace', 2, 3),
            'reverse' => RuntimeFunction::new('strrev', 1),
            'take'    => RuntimeFunction::new(__NAMESPACE__ . '\expression_function_take', 2),
        ];
    }
}

/**
 * @param int $start
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
    } else if ($end >= $start) {
        for ($num = $start; $num <= $end; $num++) {
            yield $num;
        }
    } else {
        for ($num = $start; $num >= $end; $num--) {
            yield $num;
        }
    }
}

/**
 * Returns the number of elements in a collection
 *
 * @param array|\Traversable $data
 *
 * @return int
 */
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

/**
 * Replaces one or more substrings in a string.
 *
 * @param string $string
 * @param string|string[] $search
 * @param null|string|string[] $replacement
 *
 * @return mixed
 */
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

/**
 * Glues strings from a collection together with the given separator.
 *
 * @param array|\Traversable $collection
 * @param string $glue
 *
 * @return string
 */
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

/**
 * Returns a number of the elements from the beginning of the collection.
 *
 * @param array|\Traversable $collection
 * @param int $number
 *
 * @return array|\LimitIterator
 */
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

/**
 * Skips a number of the elements from the collection.
 *
 * @param array|\Traversable $collection
 * @param int $number
 *
 * @return array|\LimitIterator
 */
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