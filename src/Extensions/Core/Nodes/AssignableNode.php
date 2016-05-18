<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

abstract class AssignableNode extends Node
{
    abstract public function compileAssign(Compiler $compiler, Node $rightHand);

    abstract public function evaluateAssign(ExecutionContext $context, $value);
}