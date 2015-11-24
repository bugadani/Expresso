<?php

namespace Expresso\Extensions;

use Expresso\Compiler\Operator;
use Expresso\Compiler\Operators\Binary\DivisionOperator;
use Expresso\Compiler\Operators\Binary\ExponentialOperator;
use Expresso\Compiler\Operators\Binary\FilterOperator;
use Expresso\Compiler\Operators\Binary\ModuloOperator;
use Expresso\Compiler\Operators\Binary\NullSafeAccessOperator;
use Expresso\Compiler\Operators\Binary\RemainderOperator;
use Expresso\Compiler\Operators\Binary\SimpleAccessOperator;
use Expresso\Compiler\Operators\Binary\AdditionOperator;
use Expresso\Compiler\Operators\Binary\ConcatenationOperator;
use Expresso\Compiler\Operators\Binary\MultiplicationOperator;
use Expresso\Compiler\Operators\Binary\SubtractionOperator;
use Expresso\Compiler\Operators\Unary\Prefix\MinusOperator;
use Expresso\Compiler\ParserCollection;
use Expresso\Compiler\Parsers\DataTokenParser;
use Expresso\Compiler\Parsers\IdentifierTokenParser;
use Expresso\Compiler\Token;
use Expresso\Extension;

class Core extends Extension
{
    public function getExtensionName()
    {
        return 'core';
    }

    public function getBinaryOperators()
    {
        return [
            //arithmetic operators
            new AdditionOperator(10),
            new SubtractionOperator(10),
            new MultiplicationOperator(11),
            new DivisionOperator(11),
            new RemainderOperator(11),
            new ModuloOperator(11),
            new ExponentialOperator(14, Operator::RIGHT),/*
            //comparison
            new EqualsOperator(7),
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
            new FilterOperator(11),/*
            new RangeOperator(9),
            new ExclusiveRangeOperator(9)
            */
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            /*new PreDecrementOperator(13, Operator::RIGHT),
            new PreIncrementOperator(13, Operator::RIGHT),*/
            new MinusOperator(13, Operator::RIGHT),
            //new NotOperator(12, Operator::RIGHT)
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
        ];
    }

    public function addParsers(ParserCollection $parserCollection)
    {
        $parserCollection->add('identifier', new IdentifierTokenParser(), Token::IDENTIFIER);
        $parserCollection->add('data', new DataTokenParser(), [Token::STRING, Token::CONSTANT]);
        $parserCollection->add('array', new ArrayDefinitionParser());
        $parserCollection->add('argumentList', new ArgumentListParser());
        $parserCollection->add('ternary', new TernaryOperatorParser());
        $parserCollection->add('prefixOperator', new PrefixOperatorParser());
        $parserCollection->add('postfixOperator', new PostfixOperatorParser());

        $parserCollection->setDefaultParser('prefixOperator');
    }


}