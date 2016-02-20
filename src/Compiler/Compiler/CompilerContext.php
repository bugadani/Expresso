<?php

namespace Expresso\Compiler\Compiler;

class CompilerContext
{
    public $source     = '';
    public $statements = [];

    public function compileStatements()
    {
        foreach ($this->statements as $statement) {
            $statement->compile();
        }
        $this->statements = [];
    }

    public function __toString()
    {
        return $this->source;
    }
}