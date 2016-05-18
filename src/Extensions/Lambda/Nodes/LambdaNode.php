<?php

namespace Expresso\Extensions\Lambda\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Runtime\RuntimeFunction;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;
use Expresso\Extensions\Core\Nodes\CallableNode;
use Recursor\Recursor;

/**
 * Class LambdaNode represents a lambda expression in the Abstract Syntax Tree.
 *
 * @package Expresso\Extensions\Lambda\Nodes
 */
class LambdaNode extends CallableNode
{
    /**
     * @var Node
     */
    private $functionBody;

    /**
     * @var string[]
     */
    private $arguments;

    /**
     * @var int
     */
    private $argumentCount;

    /**
     * LambdaNode constructor.
     *
     * @param Node $functionBody
     * @param array $arguments
     */
    public function __construct(Node $functionBody, array $arguments)
    {
        $this->arguments     = $arguments;
        $this->functionBody  = $functionBody;
        $this->argumentCount = count($this->arguments);
    }

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $functionClass = RuntimeFunction::class;
        $compiler->add("new {$functionClass}(function(");

        if ($this->argumentCount > 0) {
            $compiler->add('$' . implode(', $', $this->arguments));
        }

        $compiler->add(') use ($context) {')
                 ->add('$context = $context->createInnerScope([');
        foreach ($this->arguments as $argName) {
            $compiler->compileString($argName)
                     ->add(" => \${$argName}, ");
        }
        $compiler->add(']);');

        $compiledFunctionBody = (yield $compiler->compileNode($this->functionBody));
        $compiler->compileStatements();

        $compiler->add("return {$compiledFunctionBody};}, {$this->argumentCount})");
    }

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context)
    {
        $function = new Recursor([$this->functionBody, 'evaluate']);

        return new RuntimeFunction(function (...$args) use ($context, $function) {
            $arguments    = array_slice($args, 0, $this->argumentCount);
            $innerContext = $context->createInnerScope(array_combine($this->arguments, $arguments));

            return $function($innerContext);
        }, $this->argumentCount);
    }

    /**
     * @inheritdoc
     */
    public function getChildren() : array
    {
        return [$this->functionBody];
    }

    public function getArgumentCount() : int
    {
        return $this->argumentCount;
    }
}
