<?php

namespace Expresso\Compiler;

class ExpressionFunction
{
    private $name;
    private $callbackName;

    public function __construct($name, callable $callbackName)
    {
        $this->name         = $name;
        $this->callbackName = $callbackName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCallback() : callable
    {
        return $this->callbackName;
    }
}
