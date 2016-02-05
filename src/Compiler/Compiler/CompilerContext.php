<?php

namespace Expresso\Compiler\Compiler;

class CompilerContext
{
    public $source = '';
    public $children;
    public $statements;

    public function __construct(CompilerContext $parentContext = null)
    {
        $this->children   = new \SplDoublyLinkedList();
        $this->statements = new \SplDoublyLinkedList();
        $this->statements->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);

        if ($parentContext !== null) {
            $parentContext->children[] = $this;
        }
    }

    public function compileStatements()
    {
        foreach ($this->children as $child) {
            $child->compileStatements();
        }
        foreach ($this->statements as $statement) {
            $statement->compile();
        }
    }

    public function __toString()
    {
        return $this->source;
    }
}