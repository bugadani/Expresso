<?php

namespace Expresso;

use Expresso\Compiler\Parser\GrammarParser;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Operator;
use Expresso\Compiler\RuntimeFunction;

abstract class Extension
{
    /**
     * @return Operator[]
     */
    public function getBinaryOperators() : array
    {
        return [];
    }

    /**
     * @return Operator[]
     */
    public function getPrefixUnaryOperators() : array
    {
        return [];
    }

    /**
     * @return Operator[]
     */
    public function getPostfixUnaryOperators() : array
    {
        return [];
    }

    /**
     * @return Operator[]
     */
    public function getTernaryOperators() : array
    {
        return [];
    }

    /**
     * @return RuntimeFunction[]
     */
    public function getFunctions() : array
    {
        return [];
    }

    /**
     * Returns with an array of extension class names that will be included with the extension
     *
     * @return string[]
     */
    public function getDependencies() : array
    {
        return [];
    }

    /**
     * Defines a set of symbols for the lexer
     *
     * @return array
     */
    public function getSymbols() : array
    {
        return [];
    }

    /**
     * Override this method to extend the parser if the extension defines new language elements.
     *
     * @param GrammarParser         $parser
     * @param CompilerConfiguration $configuration
     */
    public function addParsers(GrammarParser $parser, CompilerConfiguration $configuration)
    {

    }
}
