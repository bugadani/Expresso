<?php

namespace Expresso;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operator;
use Expresso\Compiler\Parser;

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
     * Set up language extensions
     *
     * @param Parser                $parser
     * @param CompilerConfiguration $configuration
     */
    public function addParsers(Parser $parser, CompilerConfiguration $configuration)
    {

    }
}
