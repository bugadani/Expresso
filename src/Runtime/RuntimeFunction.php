<?php

namespace Expresso\Runtime;

class RuntimeFunction
{

    public static function getParameterCount(callable $function) : int
    {
        if (is_array($function)) {
            $reflection = new \ReflectionMethod($function[0], $function[1]);
        } else {
            $reflection = new \ReflectionFunction($function);
        }

        return $reflection->getNumberOfRequiredParameters();
    }

    public static function new(callable $function, int $paramCount = null, array $parameters = []) : RuntimeFunction
    {
        if ($function instanceof RuntimeFunction) {
            if (empty($parameters)) {
                return $function;
            } else {
                return $function(...$parameters);
            }
        }
        $object             = new self;
        $object->function   = $function;
        $object->paramCount = $paramCount ?? self::getParameterCount($function);
        $object->parameters = $parameters;

        return $object;
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

    protected function __construct()
    {
    }

    public function __invoke(...$args)
    {
        if (!empty($this->parameters)) {
            $args = array_merge($this->parameters, $args);
        }

        $function = $this->function;
        if (count($args) < $this->paramCount) {
            return RuntimeFunction::new($function, $this->paramCount, $args);
        } else {
            return $function(...$args);
        }
    }
}