<?php

namespace Expresso;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operator;
use Expresso\Compiler\TokenStreamParser;

abstract class Extension
{
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

    /**
     * @return Operator[]
     */
    public function getTernaryOperators()
    {
        return [];
    }

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        return [];
    }

    public function getDependencies()
    {
        return [];
    }

    public function addParsers(TokenStreamParser $parser, CompilerConfiguration $configuration)
    {

    }
}
