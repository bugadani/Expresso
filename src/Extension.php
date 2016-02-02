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

    /**
     * Defines a set of symbols for the lexer
     *
     * @return array
     */
    public function getSymbols()
    {
        return [];
    }

    /**
     * Override this method to extend the parser if the extension defines new language elements.
     *
     * @param OperatorParser        $parser
     * @param CompilerConfiguration $configuration
     */
    public function addParsers(OperatorParser $parser, CompilerConfiguration $configuration)
    {

    }
}
