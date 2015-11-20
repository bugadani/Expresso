<?php

namespace Expresso\Compiler;

abstract class Operator
{
    const LEFT = 0;
    const RIGHT = 1;
    const NONE = 2;

    private $precedence;
    private $associativity;

    public function __construct($precedence, $associativity = self::LEFT)
    {
        $this->precedence    = $precedence;
        $this->associativity = $associativity;
    }

    public function getPrecedence()
    {
        return $this->precedence;
    }

    public function isAssociativity($associativity)
    {
        return $this->associativity === $associativity;
    }

    /**
     * @return int
     */
    public function getAssociativity()
    {
        return $this->associativity;
    }

    abstract public function operators();
}
