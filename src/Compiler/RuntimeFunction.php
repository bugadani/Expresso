<?php

namespace Expresso\Compiler;

class RuntimeFunction
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
        if ($function instanceof RuntimeFunction) {
            $this->function   = $function->function;
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
        if (!empty($this->parameters)) {
            $args = array_merge($this->parameters, $args);
        }

        $function = $this->function;
        if (count($args) < $this->paramCount) {
            return new RuntimeFunction($function, $this->paramCount, $args);
        } else {
            return $function(...$args);
        }
    }
}