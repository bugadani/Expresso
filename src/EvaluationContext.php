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

    public function __construct($input, CompilerConfiguration $configuration)
    {
        parent::__construct($input);
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
}