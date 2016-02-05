<?php

namespace Expresso\Compiler\Compiler;

class TempVar extends Statement
{
    private $varName;

    public function __construct(Compiler $compiler, $varName, $source)
    {
        parent::__construct($compiler, "{$varName} = {$source}");
        $this->varName = $varName;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->varName;
    }
}