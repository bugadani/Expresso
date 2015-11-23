<?php

namespace Expresso\Extensions;

use Expresso\Extension;

class Bitwise extends Extension
{
    public function getExtensionName()
    {
        return 'bitwise';
    }

    public function getBinaryOperators()
    {
        return [
            //bitwise
            /*new BitwiseAndOperator(6),
            new BitwiseOrOperator(4),
            new BitwiseXorOperator(5),
            new ShiftLeftOperator(9),
            new ShiftRightOperator(9)*/
        ];
    }

    public function getPrefixUnaryOperators()
    {
        return [
            //new BitwiseNotOperator(13, Operator::RIGHT)
        ];
    }

    public function getPostfixUnaryOperators()
    {
        return [
        ];
    }
}