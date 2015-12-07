<?php

namespace Expresso\Compiler;

interface NodeVisitorInterface
{
    /**
     * @param Node $node
     * @return mixed
     */
    public function visit(Node $node);
}