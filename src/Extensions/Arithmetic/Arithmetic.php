<?php

namespace Expresso\Extensions\Arithmetic;

use Expresso\Compiler\Operator;
use Expresso\Extension;
use Expresso\Extensions\Arithmetic\Operators\Binary\AdditionOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\DivisibleOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\DivisionOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\ExponentialOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\GreaterThanOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\GreaterThanOrEqualsOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\LessThanOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\LessThanOrEqualsOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\ModuloOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\MultiplicationOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\NotDivisibleOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\RemainderOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\SubtractionOperator;
use Expresso\Extensions\Arithmetic\Operators\Unary\Postfix\EvenOperator;
use Expresso\Extensions\Arithmetic\Operators\Unary\Postfix\OddOperator;
use Expresso\Extensions\Arithmetic\Operators\Unary\Prefix\MinusOperator;
use Expresso\Extensions\Core\Core;

class Arithmetic extends Extension
{
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
            new ExponentialOperator(14, Operator::RIGHT),
            new DivisibleOperator(8, Operator::NONE),
            new NotDivisibleOperator(8, Operator::NONE),
            //comparison
            new LessThanOperator(8),
            new LessThanOrEqualsOperator(8),
            new GreaterThanOperator(8),
            new GreaterThanOrEqualsOperator(8)
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            new MinusOperator(13, Operator::RIGHT)
        ];
    }

    public function getPostfixUnaryOperators()
    {
        return [
            new EvenOperator(15, Operator::NONE),
            new OddOperator(15, Operator::NONE),
        ];
    }

    public function getDependencies()
    {
        return [
            Core::class
        ];
    }
}
