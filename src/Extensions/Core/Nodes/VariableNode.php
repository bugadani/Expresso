<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

abstract class VariableNode extends Node
{
    abstract public function compileAssign(Compiler $compiler, Node $rightHand);

    abstract public function evaluateAssign(ExecutionContext $context, $value);

    abstract public function compileContains(Compiler $compiler);

    abstract public function evaluateContains(ExecutionContext $context);
}