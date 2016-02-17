<?php

namespace Expresso\Compiler;

class OperatorCollection
{
    /**
     * @var Operator[]
     */
    private $operators = [];

    /**
     * @var string[]
     */
    private $symbols = [];

    /**
     * @param string   $opSymbol
     * @param Operator $operator
     */
    public function addOperator($opSymbol, Operator $operator)
    {
        if (\is_string($opSymbol)) {
            $this->symbols[] = $opSymbol;
        }
        $this->operators[ $opSymbol ] = $operator;
    }

    /**
     * Returns whether $operator is an operator symbol.
     *
     * @param $operator
     *
     * @return bool
     */
    public function isOperator($operator)
    {
        return isset($this->operators[ $operator ]);
    }

    public function getOperator($sign)
    {
        return $this->operators[ $sign ];
    }

    public function getSymbols()
    {
        return $this->symbols;
    }
}
