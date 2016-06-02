<?php

namespace Expresso\Extensions\Core;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Operator;
use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Parser\GrammarParser;
use Expresso\Compiler\Parser\Parsers\ParserReference;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Extension;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;
use Expresso\Extensions\Core\Nodes\ArrayNodes\ListNode;
use Expresso\Extensions\Core\Nodes\ArrayNodes\MapNode;
use Expresso\Extensions\Core\Nodes\ArrayNodes\RangeNode;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Extensions\Core\Nodes\StatementNode;
use Expresso\Extensions\Core\Nodes\StringNode;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\AdditionOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\DivisionOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\ExponentialOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\IntegerDivisionOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\ModuloOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\MultiplicationOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\RemainderOperator;
use Expresso\Extensions\Core\Operators\Binary\Arithmetic\SubtractionOperator;
use Expresso\Extensions\Core\Operators\Binary\ArrayAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\AssignmentOperator;
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
use Expresso\Extensions\Core\Operators\Binary\FunctionCallOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\AndOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\OrOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\XorOperator;
use Expresso\Extensions\Core\Operators\Binary\NullSafeAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;
use Expresso\Extensions\Core\Operators\Binary\Strings\ConcatenationOperator;
use Expresso\Extensions\Core\Operators\Binary\Test\DivisibleOperator;
use Expresso\Extensions\Core\Operators\Binary\Test\NotDivisibleOperator;
use Expresso\Extensions\Core\Operators\Ternary\TernaryConditionalOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\EvenOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsNotSetOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsSetOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\OddOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\BitwiseNotOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\MinusOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;
use Expresso\Extensions\Core\Parsers\OperatorParser;
use Expresso\Runtime\RuntimeFunction;

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
        return [',', '[', ']', '(', ')', '{', '}', ':', '?', '\\', '=>', ';', '...'];
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
        $symbol         = function ($symbol) {
            return TokenParser::create(Token::SYMBOL, $symbol);
        };
        $operatorParser = function ($operators) use ($pushOperator) {
            return TokenParser::create(Token::OPERATOR, [$operators, 'isOperator'])
                              ->process($pushOperator($operators));
        };
        $prefixParser   = $operatorParser($prefixOperators);
        $binaryParser   = $operatorParser($binaryOperators);
        $postfixParser  = $operatorParser($postfixOperators);

        $identifier      = TokenParser::create(Token::IDENTIFIER);
        $constantData    = TokenParser::create(Token::CONSTANT);
        $constantString  = TokenParser::create(Token::STRING);
        $endOfExpression = TokenParser::create(Token::EOF);

        $expression  = new ParserReference($parserContainer, 'expression');
        $operand     = new ParserReference($parserContainer, 'operand');
        $listSubtype = new ParserReference($parserContainer, 'listSubtypes');

        //Other parsers
        $mapParser = function ($separatorSymbol) use ($expression, $symbol) {
            $separator = TokenParser::create(Token::SYMBOL, $separatorSymbol);

            return $separator
                ->followedBy($expression)
                ->followedBy(
                    $symbol(',')
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
                )->process(function (array $children, AbstractParser $parent) {
                    $parent->getParent()
                           ->overrideProcess(function (array $children) {
                               list($firstKey, list($firstValue, $elements)) = $children;
                               $node = new MapNode();
                               $node->add($firstKey, $firstValue);
                               if (!empty($elements)) {
                                   foreach ($elements as list($key, $value)) {
                                       $node->add($key, $value);
                                   }
                               }

                               return $node;
                           });
                    list(, $firstValue, $elements) = $children;

                    return [$firstValue, $elements];
                });
        };

        $listParser = $symbol(',')
            ->followedBy($expression
                ->repeatSeparatedBy($symbol(',')))
            ->process($returnArgument(1));

        $listTypes = $listParser
            ->orA($symbol('...')
                ->followedBy($expression->optional())
                ->process(function ($ch, AbstractParser $parent) {
                    $parent->getParent()
                           ->overrideProcess(function (array $children) {
                               return new RangeNode($children[0], $children[1]);
                           });

                    return $ch[1];
                }))
            ->orA($mapParser(':'))
            ->orA($mapParser('=>'));

        $arrayElementList = $expression
            ->followedBy($listSubtype->optional());

        $arrayDefinition = $symbol('[')
            ->followedBy($arrayElementList
                ->optional()
                ->process(function ($children) {
                    if ($children === null) {
                        return new ListNode();
                    } else if (is_array($children)) {
                        list($first, $array) = $children;
                        $node = new ListNode();
                        $node->add($first);
                        if (!empty($array)) {
                            foreach ($array as $item) {
                                $node->add($item);
                            }
                        }

                        return $node;
                    }

                    return $children;
                }))
            ->followedBy($symbol(']'))
            ->process($returnArgument(1));

        $argumentList = $symbol('?')
            ->orA($expression)
            ->repeatSeparatedBy($symbol(','));

        $functionCall = $symbol('(')
            ->followedBy($argumentList->optional())
            ->followedBy($symbol(')'));

        $arrayAccess = $symbol('[')
            ->followedBy($expression)
            ->followedBy($symbol(']'));

        $prefixOperatorSequence = $prefixParser->repeated();

        $dereferenceSequence = $functionCall
            ->orA($arrayAccess)
            ->repeated();

        $postfixOperatorSequence = $postfixParser
            ->repeated()
            ->optional();

        $expressions = $expression
            ->repeated()
            ->process([StatementNode::class, 'create']);

        $groupedExpression = $symbol('(')
            ->followedBy($expression)
            ->followedBy($symbol(')'))
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

        $binaryExpression = $term
            ->repeatSeparatedBy($binaryParser);

        $conditionalExpressionSuffix = $symbol('?')
            ->followedBy($expression)
            ->followedBy($symbol(':'))
            ->followedBy($expression);

        $expressionParser = $binaryExpression
            ->followedBy($conditionalExpressionSuffix->optional());

        $conditionalExpressionSuffix->process(
            function (array $children, AbstractParser $parent) use ($parser, $conditionalOperator) {
                $parent->overrideProcess(function ($children) use ($parser, $conditionalOperator) {
                    list($left, list($middle, $right)) = $children;
                    $parser->pushOperator($conditionalOperator);

                    $parser->pushOperand($middle);
                    $parser->pushOperand($right);

                    return $parser->popOperatorSentinel();
                });

                list(, $middle, , $right) = $children;

                return [$middle, $right];
            }
        );

        $block = $symbol('{')
            ->followedBy($expressions)
            ->followedBy($symbol('}'))
            ->process($returnArgument(1));

        $statement = $expression->orA($block);

        $program = $expressions->orA($block)
                               ->followedBy($endOfExpression)
                               ->process($returnArgument(0));

        //Set processor functions
        $identifier->process($returnNode(IdentifierNode::class));
        $constantData->process($returnNode(DataNode::class));
        $constantString->process($returnNode(StringNode::class));
        $operandParser->process([$parser, 'pushOperand']);

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

        $expressionParser
            ->runBefore([$parser, 'pushOperatorSentinel'])
            ->process([$parser, 'popOperatorSentinel']);

        $parserContainer->set('operand', $operandParser);
        $parserContainer->set('expression', $expressionParser);
        $parserContainer->set('statement', $statement);
        $parserContainer->set('listSubtypes', $listTypes);
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