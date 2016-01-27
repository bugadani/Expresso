<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;
use Expresso\Extensions\Lambda\Nodes\LambdaNode;

class GeneratorFilterNode extends Node
{
    /**
     * @var Node
     */
    private $functionBody;

    public function __construct(Node $functionBody)
    {
        $this->functionBody = $functionBody;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('function(array $arguments) use ($context) {')
                 ->add('$context = $context->createInnerScope($arguments);');

        $compiledFunctionBody = (yield $compiler->compileNode($this->functionBody));

        $compiler->compileTempVariables();
        $compiler->add('return ');
        $compiler->add($compiledFunctionBody->source);
        $compiler->add(';}');
    }

    public function evaluate(EvaluationContext $context)
    {
        yield function (array $arguments) use ($context) {
            $innerContext = $context->createInnerScope($arguments);

            return GeneratorHelper::executeGeneratorsRecursive(
                $this->functionBody->evaluate($innerContext)
            );
        };
    }

    public function getChildren()
    {
        return [$this->functionBody];
    }
}