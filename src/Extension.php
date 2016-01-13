<?php

namespace Expresso;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operator;
use Expresso\Compiler\Parser\OperatorParser;

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

    /**
     * Returns with an array of extension class names that will be included with the extension
     *
     * @return string[]
     */
    public function getDependencies()
    {
        return [];
    }

    public function getSymbols()
    {
        return [];
    }

    /**
     * Set up language extensions
     *
     * @param OperatorParser        $parser
     * @param CompilerConfiguration $configuration
     */
    public function addParsers(OperatorParser $parser, CompilerConfiguration $configuration)
    {

    }
}
