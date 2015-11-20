<?php

namespace Expresso\Compiler;

interface NodeInterface
{

    public function compile(Compiler $compiler);

    public function evaluate(ExecutionContext $context);
}