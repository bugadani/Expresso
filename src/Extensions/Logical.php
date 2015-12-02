<?php

namespace Expresso\Extensions;

use Expresso\Compiler\Operator;
use Expresso\Extension;
use Expresso\Extensions\Logical\Operators\Binary\AndOperator;
use Expresso\Extensions\Logical\Operators\Unary\Prefix\NotOperator;

class Logical extends Extension
{
    public function getBinaryOperators()
    {
        return [
            //logical
            new AndOperator(3),
            //new OrOperator(2),
            //new XorOperator(1),
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            new NotOperator(12, Operator::RIGHT)
        ];
    }

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\Core'
        ];
    }
}
