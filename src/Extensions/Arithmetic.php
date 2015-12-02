<?php

namespace Expresso\Extensions;

use Expresso\Compiler\Operator;
use Expresso\Extension;
use Expresso\Extensions\Arithmetic\Operators\Binary\AdditionOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\DivisionOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\ExponentialOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\ModuloOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\MultiplicationOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\RemainderOperator;
use Expresso\Extensions\Arithmetic\Operators\Binary\SubtractionOperator;
use Expresso\Extensions\Arithmetic\Operators\Unary\Prefix\MinusOperator;

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
            new ExponentialOperator(14, Operator::RIGHT)
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
        ];
    }

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\Core'
        ];
    }
}
