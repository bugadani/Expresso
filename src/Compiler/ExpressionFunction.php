<?php

namespace Expresso\Compiler;

class ExpressionFunction
{
    private $name;
    private $callbackName;

    public function __construct($name, $callbackName)
    {
        $this->name         = $name;
        $this->callbackName = $callbackName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFunctionName()
    {
        return $this->callbackName;
    }
}