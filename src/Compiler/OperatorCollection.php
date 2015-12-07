<?php

namespace Expresso\Compiler;

class OperatorCollection
{
    /**
     * @var Operator[]
     */
    private $operators = [];

    public function exists(Operator $operator)
    {
        return in_array($operator, $this->operators, true);
    }

    /**
     * @param Operator $operator
     */
    public function addOperator(Operator $operator)
    {
        foreach ((array)$operator->operators() as $opSymbol) {
            $this->operators[ $opSymbol ] = $operator;
        }
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
        return array_keys($this->operators);
    }
}
