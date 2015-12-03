<?php

namespace Expresso\Extensions;

use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operator;
use Expresso\Extension;
use Expresso\Extensions\Bitwise\Operators\Binary\AndOperator;
use Expresso\Extensions\Bitwise\Operators\Binary\OrOperator;
use Expresso\Extensions\Bitwise\Operators\Binary\XorOperator;
use Expresso\Extensions\Bitwise\Operators\Binary\LeftArithmeticShiftOperator;
use Expresso\Extensions\Bitwise\Operators\Binary\RightArithmeticShiftOperator;
use Expresso\Extensions\Bitwise\Operators\Unary\Prefix\BitwiseNotOperator;

class Bitwise extends Extension
{
    public function getBinaryOperators()
    {
        return [
            //bitwise
            new AndOperator(6),
            new OrOperator(4),
            new XorOperator(5),
            new LeftArithmeticShiftOperator(9),
            new RightArithmeticShiftOperator(9)
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            new BitwiseNotOperator(13, Operator::RIGHT)
        ];
    }

    public function getPostfixUnaryOperators()
    {
        return [
        ];
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction('popcount', __NAMESPACE__ . '\\expression_function_population_count')
        ];
    }


    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\Core'
        ];
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