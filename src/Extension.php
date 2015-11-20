<?php

namespace Expresso;

use Expresso\Compiler\Operator;

abstract class Extension
{
    abstract public function getExtensionName();

    /**
     * @return Operator[]
     */
    public function getBinaryOperators()
    {
        return [];
    }

    /**
     * @return Operator[]
     */
    public function getPrefixUnaryOperators()
    {
        return [];
    }

    /**
     * @return Operator[]
     */
    public function getPostfixUnaryOperators()
    {
        return [];
    }
}
