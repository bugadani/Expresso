<?php

namespace Expresso\Compiler;

class CurriedFunctionWrapper
{

    public static function getParameterCount(callable $function)
    {
        if (is_array($function)) {
            $reflection = new \ReflectionMethod($function[0], $function[1]);
        } else {
            $reflection = new \ReflectionFunction($function);
        }

        return $reflection->getNumberOfRequiredParameters();
    }

    /**
     * @var callable
     */
    private $function;

    /**
     * @var int
     */
    private $paramCount;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(callable $function, int $paramCount = null, array $parameters = [])
    {
        if ($function instanceof CurriedFunctionWrapper) {
            $this->function = $function->function;
            $this->paramCount = $function->paramCount;
            $this->parameters = $function->parameters + $parameters;
        } else {
            $this->function   = $function;
            $this->paramCount = $paramCount ?? self::getParameterCount($function);
            $this->parameters = $parameters;
        }
    }

    public function __invoke(...$args)
    {
        if ((count($args) + count($this->parameters)) < $this->paramCount) {
            return new CurriedFunctionWrapper($this->function, $this->paramCount, $this->parameters + $args);
        } else {
            $function = $this->function;

            return $function(...$this->parameters, ...$args);
        }
    }
}