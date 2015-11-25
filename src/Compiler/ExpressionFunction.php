<?php

namespace Expresso\Compiler;

class ExpressionFunction
{
    private $name;
    private $called;

    public function __construct($name, $called)
    {
        $this->name = $name;
        $this->called = $called;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFunctionName()
    {
        return $this->called;
    }

    public function call(array $arguments = [])
    {
        return call_user_func_array($this->called, $arguments);
    }
}