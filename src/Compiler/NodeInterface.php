<?php

namespace Expresso\Compiler;

use Expresso\EvaluationContext;

interface NodeInterface
{

    public function compile(Compiler $compiler);

    public function evaluate(EvaluationContext $context);
}