<?php

namespace Expresso\Compiler;

interface NodeVisitorInterface
{
    public function enterNode(Node $node);
    public function leaveNode(Node $node);
}