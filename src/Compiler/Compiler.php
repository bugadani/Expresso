<?php

namespace Expresso\Compiler;

class Compiler
{
    private $source;

    public function add($string)
    {
        $this->source .= $string;

        return $this;
    }

    public function compileNode(NodeInterface $node)
    {
        $node->compile($this);

        return $this;
    }

    public function compile(NodeInterface $rootNode)
    {
        $this->source = '';
        $rootNode->compile($this);

        return $this->source;
    }
}