<?php

namespace Expresso\Runtime;

class RuntimeFunction
{

    public static function getParameterCount(callable $function) : int
    {
        if ($function instanceof RuntimeFunction) {
            return $function->paramCount;
        } else if (is_array($function)) {
            $reflection = new \ReflectionMethod($function[0], $function[1]);
        } else if (!is_string($function) || function_exists($function)) {
            $reflection = new \ReflectionFunction($function);
        } else {
            $reflection = new \ReflectionMethod($function);
        }

        return $reflection->getNumberOfRequiredParameters();
    }

    public static function new(callable $function, int $paramCount = null, array $parameters = []) : RuntimeFunction
    {
        $parameterCount = $paramCount ?? self::getParameterCount($function);

        $missingParams = [];
        for ($i = 0; $i < $parameterCount; $i++) {
            if (!isset($parameters[ $i ]) || $parameters[ $i ] instanceof PlaceholderArgument) {
                $missingParams[] = $i;
                unset($parameters[ $i ]);
            }
        }

        $object                    = new self;
        $object->function          = $function;
        $object->paramCount        = count($missingParams);
        $object->fixedParameters   = $parameters;
        $object->missingParameters = $missingParams;

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
    private $fixedParameters;

    /**
     * @var array
     */
    private $missingParameters;

    protected function __construct()
    {
    }

    public function __invoke(...$args)
    {
        if (count($args) < $this->paramCount) {
            return RuntimeFunction::new($this, $this->paramCount, $args);
        } else {
            $arguments = $this->fixedParameters;

            //Merge fixed and passed arguments
            for ($i = count($this->missingParameters) - 1; $i >= 0; $i--) {
                $arguments[ $this->missingParameters[ $i ] ] = array_pop($args);
            }
            ksort($arguments);

            $arguments = array_merge($args, $arguments);

            $function = $this->function;

            return $function(...$arguments);
        }
    }
}