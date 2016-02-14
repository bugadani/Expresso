<?php

namespace Expresso\Compiler\Compiler;

class CompilerContext
{
    public $source     = '';
    public $children   = [];
    public $statements = [];

    public function __construct(CompilerContext $parentContext = null)
    {
        if ($parentContext !== null) {
            $parentContext->children[] = $this;
        }
    }

    public function flatten()
    {
        foreach ($this->children as $child) {
            $this->statements = array_merge($this->statements, $child->statements);
        }
        $this->children = [];
    }

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