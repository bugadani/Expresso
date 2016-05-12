<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\ExecutionContext;
use Expresso\Expresso;

class CompiledExpression
{
    /**
     * @var Expresso
     */
    private $configuration;

    /**
     * @var callable
     */
    private $function;

    public function __construct(CompilerConfiguration $configuration, callable $function)
    {
        $this->configuration = $configuration;
        $this->function = $function;
    }

    public function __invoke(array $args)
    {
        $function = $this->function;
        return $function(new ExecutionContext($args, $this->configuration));
    }
}