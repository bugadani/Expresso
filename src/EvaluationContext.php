<?php

namespace Expresso;

use Expresso\Compiler\CompilerConfiguration;
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
     * @return ExpressionFunction
     */
    public function getFunction($functionName)
    {
        return $this->configuration->getFunctions()[ $functionName ];
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function createInnerScope($input)
    {
        return new EvaluationContext($input, $this->configuration, $this);
    }
}