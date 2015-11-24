<?php

namespace Expresso;

use Expresso\Compiler\Operator;
use Expresso\Compiler\ParserCollection;

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

    public function addParsers(ParserCollection $parserCollection)
    {

    }
}
