<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\Exceptions\AssignmentException;
use Expresso\Runtime\RuntimeFunction;

class PropertyAccessNode extends AccessNode
{
    public function compileAssign(Compiler $compiler, Node $rightHand)
    {
        if (!$this->left instanceof VariableNode) {
            throw new AssignmentException('Cannot assign to non-variable');
        }

        $assignmentExceptionClass = AssignmentException::class;

        $leftSource  = yield $compiler->compileNode($this->left);
        $rightSource = yield $compiler->compileNode($this->right);
        $value       = yield $compiler->compileNode($rightHand);

        $compiler->addStatement("if (is_array({$leftSource})) {
            {$leftSource}[ {$rightSource} ] = {$value};
        } else if (is_object({$leftSource})) {
            if ({$leftSource} instanceof \\ArrayAccess) {
                {$leftSource}[ {$rightSource} ] = {$value};
            } else if (property_exists({$leftSource}, {$rightSource})) {
                {$leftSource}->{{$rightSource}} = {$value};
            } else {
                throw new {$assignmentExceptionClass}();
            }
        }");
        $compiler->add($value);
    }

    public function compile(Compiler $compiler)
    {
        $leftSource    = yield $compiler->compileNode($this->left);
        $rightSource   = yield $compiler->compileNode($this->right);
        $functionClass = RuntimeFunction::class;

        if (!$this->left instanceof IdentifierNode) {
            $leftSource = $compiler->addTempVariable($leftSource);
        }

        $tempVar = $compiler->requestTempVariable();
        $compiler->addStatement("if (is_array({$leftSource})) {
            if (!isset({$leftSource}[ {$rightSource} ])) {
                throw new \\OutOfBoundsException();
            }
            {$tempVar} =& {$leftSource}[ {$rightSource} ];
        } else if (is_object({$leftSource})) {
            if (method_exists({$leftSource}, {$rightSource})) {
                {$tempVar} = {$functionClass}::new([{$leftSource}, {$rightSource}]);
            } else if ({$leftSource} instanceof \\ArrayAccess) {
                if (!isset({$leftSource}[ {$rightSource} ])) {
                    throw new \\OutOfBoundsException();
                }
                {$tempVar} =& {$leftSource}[ {$rightSource} ];
            } else if (property_exists({$leftSource}, {$rightSource})) {
                {$tempVar} =& {$leftSource}->{{$rightSource}};
            } else {
                throw new \\OutOfBoundsException();
            }
        }");

        $compiler->add($tempVar);
    }

    protected function &get(&$container, $rightHand)
    {
        if (is_array($container)) {
            if (isset($container[ $rightHand ])) {
                return $container[ $rightHand ];
            }
        } else if (is_object($container)) {
            if (method_exists($container, $rightHand)) {
                $methodWrapper = RuntimeFunction::new([$container, $rightHand]);

                //intentionally multiple lines because only variables can be returned by reference
                return $methodWrapper;
            } else if ($container instanceof \ArrayAccess) {
                if (isset($container[ $rightHand ])) {
                    return $container[ $rightHand ];
                }
            } else if (property_exists($container, $rightHand)) {
                return $container->{$rightHand};
            }
        }

        throw new \OutOfBoundsException("{$rightHand} is not present in \$container");
    }

    protected function contains($container, $leftHand) : bool
    {
        if (is_array($container)) {
            return isset($container[ $leftHand ]);
        } else {
            return (method_exists($container, $leftHand) || property_exists($container, $leftHand));
        }
    }

    protected function assign(&$container, $leftHand, $rightHand)
    {
        if (is_object($container)) {
            $container->{$leftHand} = $rightHand;
        } else {
            $container[ $leftHand ] = $rightHand;
        }
    }

    public function compileContains(Compiler $compiler)
    {
        $compiler->pushContext();
        $tempVar = $compiler->requestTempVariable();
        $compiler->add('try {');
        yield $compiler->compileNode($this);
        $compiler->compileStatements();
        $compiler->add("{$tempVar} = true;} catch(\\OutOfBoundsException \$e) {{$tempVar} = false;}");
        $compiler->addStatement($compiler->popContext());
        $compiler->add($tempVar);
    }
}
