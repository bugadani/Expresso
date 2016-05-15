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
        $function = $this->function;
        if (count($this->parameters) > 0) {
            if ((func_num_args() + count($this->parameters)) < $this->paramCount) {
                return new RuntimeFunction($function, $this->paramCount, array_merge($this->parameters, $args));
            } else {
                return $function(...array_merge($this->parameters, $args));
            }
        } else {
            if (func_num_args() < $this->paramCount) {
                return new RuntimeFunction($function, $this->paramCount, $args);
            } else {
                return $function(...$args);
            }
        }

    }
}