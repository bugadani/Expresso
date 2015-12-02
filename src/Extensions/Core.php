<?php

namespace Expresso\Extensions;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operator;
use Expresso\Compiler\Operators\Binary\ConcatenationOperator;
use Expresso\Compiler\Operators\Binary\FilterOperator;
use Expresso\Compiler\Operators\Binary\NullSafeAccessOperator;
use Expresso\Compiler\Operators\Binary\RangeOperator;
use Expresso\Compiler\Operators\Binary\SimpleAccessOperator;
use Expresso\Compiler\Operators\FunctionCallOperator;
use Expresso\Compiler\Operators\Unary\Postfix\InfiniteRangeOperator;
use Expresso\Compiler\Operators\Unary\Prefix\NotOperator;
use Expresso\Compiler\ParserAlternativeCollection;
use Expresso\Compiler\Parsers\ArgumentListParser;
use Expresso\Compiler\Parsers\ArrayAccessParser;
use Expresso\Compiler\Parsers\ArrayDefinitionParser;
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

class Core extends Extension
{
    public function getBinaryOperators()
    {
        return [
            //comparison
            /*new EqualsOperator(7),
            new IdenticalOperator(7),
            new NotIdenticalOperator(7),
            new NotEqualsOperator(7),
            new LessThanOperator(8),
            new LessThanOrEqualsOperator(8),
            new GreaterThanOperator(8),
            new GreaterThanOrEqualsOperator(8),
            //logical
            new AndOperator(3),
            new OrOperator(2),
            new XorOperator(1),
            //test
            new ContainsOperator(8, Operator::NONE),
            new EndsOperator(8, Operator::NONE),
            new MatchesOperator(8, Operator::NONE),
            new NotContainsOperator(8, Operator::NONE),
            new NotEndsOperator(8, Operator::NONE),
            new NotMatchesOperator(8, Operator::NONE),
            new NotStartsOperator(8, Operator::NONE),
            new StartsOperator(8, Operator::NONE),
            new DivisibleByOperator(8, Operator::NONE),
            new NotDivisibleByOperator(8, Operator::NONE),
            //other
            new NullCoalescingOperator(1),*/
            new ConcatenationOperator(10),
            new SimpleAccessOperator(16),
            new NullSafeAccessOperator(16),
            new FilterOperator(11),
            new RangeOperator(9),/*
            new ExclusiveRangeOperator(9)
            */
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            /*new PreDecrementOperator(13, Operator::RIGHT),
            new PreIncrementOperator(13, Operator::RIGHT),*/
            new NotOperator(12, Operator::RIGHT)
        ];
    }

    public function getPostfixUnaryOperators()
    {
        return [
            /*new IsSetOperator(15, Operator::RIGHT),
             new IsNotSetOperator(15, Operator::RIGHT),
             new EvenOperator(15, Operator::NONE),
             new OddOperator(15, Operator::NONE),
             new PostDecrementOperator(15),
             new PostIncrementOperator(15),
             new EmptyOperator(15),
             new NotEmptyOperator(15)*/
            new InfiniteRangeOperator(15, Operator::NONE)
        ];
    }

    public function addParsers(TokenStreamParser $parser, CompilerConfiguration $configuration)
    {
        $tokenParsers          = new ParserAlternativeCollection(
            new PrefixOperatorParser($configuration->getPrefixOperators())
        );
        $postfixOperatorParser = new PostfixOperatorParser($configuration->getUnaryOperators());
        $identifierParser      = new IdentifierParser();

        $tokenParsers->addAlternative($identifierParser, Token::IDENTIFIER);
        $tokenParsers->addAlternative(new DataTokenParser(), Token::CONSTANT);
        $tokenParsers->addAlternative(new DataTokenParser(), Token::STRING);
        $tokenParsers->addAlternative(new ParenthesisGroupedExpressionParser(), [Token::PUNCTUATION, '(']);
        $tokenParsers->addAlternative(new ArrayDefinitionParser(), [Token::PUNCTUATION, '[']);

        $postfixParsers = new ParserAlternativeCollection($postfixOperatorParser);
        $postfixParsers->addAlternative(
            new FunctionCallParser(new FunctionCallOperator(11, $configuration->getFunctions())),
            [Token::PUNCTUATION, '(']
        );
        $postfixParsers->addAlternative(new ArrayAccessParser(), [Token::PUNCTUATION, '[']);

        $postfixNoFcParsers = new ParserAlternativeCollection($postfixOperatorParser);
        $postfixNoFcParsers->addAlternative(new ArrayAccessParser(), [Token::PUNCTUATION, '[']);

        $expressionParser = new ExpressionParser();

        $parser->addParser('term', $tokenParsers);
        $parser->addParser('identifier', $identifierParser);
        $parser->addParser('binary', new BinaryOperatorParser($configuration->getBinaryOperators()));
        $parser->addParser('postfix', $postfixParsers);
        $parser->addParser('postfix no function call', $postfixNoFcParsers);
        $parser->addParser('expression', $expressionParser);
        $parser->addParser('conditional', new ConditionalParser());
        $parser->addParser('argumentList', new ArgumentListParser());

        $parser->setDefaultParser($expressionParser);
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction('count', 'count'),
            new ExpressionFunction('join', __NAMESPACE__ . '\expression_function_join'),
            new ExpressionFunction('skip', __NAMESPACE__ . '\expression_function_skip'),
            new ExpressionFunction('replace', __NAMESPACE__ . '\expression_function_replace'),
            new ExpressionFunction('reverse', 'strrev'),
            new ExpressionFunction('take', __NAMESPACE__ . '\expression_function_take'),
        ];
    }
}

function expression_function_replace($string, $search, $replacement)
{
    return str_replace($search, $replacement, $string);
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