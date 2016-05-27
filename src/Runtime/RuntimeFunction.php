<?php

namespace Expresso\Runtime;

class RuntimeFunction
{

    public static function new(callable $function, int $paramCount = null, int $maxParamCount = null, array $parameters = []) : RuntimeFunction
    {
        if ($paramCount === null || $maxParamCount === null) {
            if($maxParamCount === null && $paramCount !== null) {
                $maxParamCount = $paramCount;
            } else if ($function instanceof RuntimeFunction) {
                $paramCount    = $function->paramCount;
                $maxParamCount = $function->getMaxParamCount();
            } else {
                if (is_array($function)) {
                    $reflection = new \ReflectionMethod($function[0], $function[1]);
                } else if (!is_string($function) || function_exists($function)) {
                    $reflection = new \ReflectionFunction($function);
                } else {
                    $reflection = new \ReflectionMethod($function);
                }

                $paramCount    = $reflection->getNumberOfRequiredParameters();
                $maxParamCount = $reflection->getNumberOfParameters();
            }
        }

        $missingParams = [];
        for ($i = 0; $i < $paramCount; $i++) {
            if (!isset($parameters[ $i ]) || $parameters[ $i ] instanceof PlaceholderArgument) {
                $missingParams[] = $i;
                unset($parameters[ $i ]);
            }
        }

        $object                    = new self;
        $object->function          = $function;
        $object->paramCount        = count($missingParams);
        $object->maxParamCount     = $maxParamCount - count($parameters);
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
    private $paramCount = 0;

    /**
     * @var int
     */
    private $maxParamCount = 0;

    /**
     * @var array
     */
    private $fixedParameters = [];

    /**
     * @var array
     */
    private $missingParameters = [];

    protected function __construct()
    {
    }

    private function getMaxParamCount() : int
    {
        if ($this->function instanceof RuntimeFunction) {
            return $this->function->getMaxParamCount() - count($this->fixedParameters);
        }

        return $this->maxParamCount;
    }

    public function __invoke(...$args)
    {
        if (count($args) < $this->paramCount) {
            return RuntimeFunction::new($this, $this->paramCount, $this->maxParamCount, $args);
        } else {
            $arguments = $this->fixedParameters;

            //Chop off extra arguments
            $args = array_slice($args, 0, $this->getMaxParamCount());
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