<?php

namespace Expresso\Extensions\Lambda\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class LambdaOperator extends BinaryOperator
{

    public function operators()
    {
        return '->';
    }
}