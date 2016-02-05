<?php

namespace Expresso\Compiler\Compiler;

class Statement
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Compiler $compiler, $source)
    {
        $this->source = $source;
        $this->compiler = $compiler;
    }

    public function compile()
    {
        $this->compiler->add($this->source . ";\n");
    }
}