<?php

namespace Expresso\Extensions\Core;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operator;
use Expresso\Compiler\Operators\FunctionCallOperator;
use Expresso\Compiler\ParserAlternativeCollection;
use Expresso\Compiler\Parsers\BinaryOperatorParser;
use Expresso\Compiler\Parsers\ConditionalParser;
use Expresso\Compiler\Parsers\DataTokenParser;
use Expresso\Compiler\Parsers\ExpressionParser;
use Expresso\Compiler\Parsers\FunctionCallParser;
use Expresso\Compiler\Parsers\IdentifierParser;
use Expresso\Compiler\Parsers\ParenthesisGroupedExpressionParser;
use Expresso\Compiler\Parsers\PostfixOperatorParser;
use Expresso\Compiler\Parsers\PrefixOperatorParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extension;
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
use Expresso\Extensions\Core\Parsers\ArrayAccessParser;
use Expresso\Extensions\Core\Parsers\ArrayDefinitionParser;

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

    public function addParsers(TokenStreamParser $parser, CompilerConfiguration $configuration)
    {
        $binaryOperatorParser    = new BinaryOperatorParser($configuration->getBinaryOperators());
        $prefixOperatorParser    = new PrefixOperatorParser($configuration->getPrefixOperators());
        $postfixOperatorParser   = new PostfixOperatorParser($configuration->getUnaryOperators());
        $ternaryOperatorParser   = new ConditionalParser($configuration->getOperatorByClass(ConditionalOperator::class));
        $identifierParser        = new IdentifierParser();
        $arrayAccessParser       = new ArrayAccessParser($configuration->getOperatorByClass(ArrayAccessOperator::class));
        $dataTokenParser         = new DataTokenParser();
        $groupedExpressionParser = new ParenthesisGroupedExpressionParser();
        $arrayDefinitionParser   = new ArrayDefinitionParser();
        $functionCallParser      = new FunctionCallParser($configuration->getOperatorByClass(FunctionCallOperator::class));
        $expressionParser        = new ExpressionParser();

        $tokenParsers = new ParserAlternativeCollection($prefixOperatorParser);
        $tokenParsers->addAlternative($identifierParser, Token::IDENTIFIER);
        $tokenParsers->addAlternative($dataTokenParser, Token::CONSTANT);
        $tokenParsers->addAlternative($dataTokenParser, Token::STRING);
        $tokenParsers->addAlternative($groupedExpressionParser, [Token::PUNCTUATION, '(']);
        $tokenParsers->addAlternative($arrayDefinitionParser, [Token::PUNCTUATION, '[']);

        $postfixParsers = new ParserAlternativeCollection($postfixOperatorParser);
        $postfixParsers->addAlternative($functionCallParser, [Token::PUNCTUATION, '(']);
        $postfixParsers->addAlternative($arrayAccessParser, [Token::PUNCTUATION, '[']);

        $postfixNoFcParsers = new ParserAlternativeCollection($postfixOperatorParser);
        $postfixNoFcParsers->addAlternative($arrayAccessParser, [Token::PUNCTUATION, '[']);

        $parser->addParser('term', $tokenParsers);
        $parser->addParser('identifier', $identifierParser);
        $parser->addParser('binary', $binaryOperatorParser);
        $parser->addParser('postfix', $postfixParsers);
        $parser->addParser('postfix no function call', $postfixNoFcParsers);
        $parser->addParser('expression', $expressionParser);
        $parser->addParser('conditional', $ternaryOperatorParser);

        $parser->setDefaultParserName('expression');
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

function expression_function_population_count($data)
{
    if ($data & 0x00000000 > 0) {
        //64 bits, not yet supported
        return 0;
    } else {
        $data -= (($data >> 1) & 0x55555555);
        $data = ((($data >> 2) & 0x33333333) + ($data & 0x33333333));
        $data = ((($data >> 4) + $data) & 0x0f0f0f0f);
        $data += ($data >> 8);
        $data += ($data >> 16);

        return ($data & 0x0000003f);
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