<?php

namespace Expresso\Compiler;

use Expresso\ExecutionContext;

interface NodeInterface
{

    public function compile(Compiler $compiler);

    public function evaluate(ExecutionContext $context);
}