<?php

namespace Expresso;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;

class EvaluationContext extends ExecutionContext
{
    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    public function __construct($input, CompilerConfiguration $configuration, EvaluationContext $parentScope = null)
    {
        parent::__construct($input, $parentScope);
        $this->configuration = $configuration;
    }

    /**
     * @param $functionName
     *
     * @return ExpressionFunction
     */
    public function getFunction($functionName)
    {
        $functions = $this->configuration->getFunctions();

        if (isset($functions[ $functionName ])) {
            return $functions[ $functionName ];
        }

        if (isset($this[ $functionName ])) {
            return $this[ $functionName ];
        }

        if (isset($this->parentContext)) {
            return $this->parentContext->getFunction($functionName);
        } else {
            throw new \OutOfBoundsException('Function not found: ' . $functionName);
        }
    }

    public function createInnerScope($input)
    {
        return new EvaluationContext($input, $this->configuration, $this);
    }
}