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

    private $returnValue;

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

    /**
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * @param mixed $returnValue
     */
    public function setReturnValue($returnValue)
    {
        $this->returnValue = $returnValue;
    }
}