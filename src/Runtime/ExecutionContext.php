<?php

namespace Expresso\Runtime;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Runtime\Exceptions\AssignmentException;

class ExecutionContext implements \ArrayAccess
{
    /**
     * @var ExecutionContext
     */
    private $parentContext;

    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    /**
     * @var array
     */
    private $data;

    public function __construct(array $input, CompilerConfiguration $configuration, ExecutionContext $parentContext = null)
    {
        $this->data          = $input;
        $this->configuration = $configuration;
        $this->parentContext = $parentContext ?? new class($configuration) extends ExecutionContext
            {
                private $configuration;

                public function __construct(CompilerConfiguration $config)
                {
                    $this->configuration = $config;
                }

                public function &offsetGet($index)
                {
                    if ($this->configuration->hasFunction($index)) {
                        return $this->configuration->getFunctions()[ $index ];
                    }
                    throw new \OutOfBoundsException("Array index out of bounds: '{$index}'");
                }

                public function offsetExists($offset) : bool
                {
                    return false;
                }

                public function getFunction($functionName) : RuntimeFunction
                {
                    throw new \OutOfBoundsException("Function not found: '{$functionName}'");
                }
            };
    }

    public function createInnerScope($input) : ExecutionContext
    {
        return new ExecutionContext($input, $this->configuration, $this);
    }

    /**
     * @inheritdoc
     */
    public function &offsetGet($index)
    {
        if (array_key_exists($index, $this->data)) {
            return $this->data[ $index ];
        }

        return $this->parentContext->offsetGet($index);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset) : bool
    {
        return array_key_exists($offset, $this->data) || $this->parentContext->offsetExists($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if ($this->configuration->hasFunction($offset)) {
            throw new AssignmentException("Cannot assign value to predefined function '{$offset}'");
        }
        if ($this->parentContext->offsetExists($offset)) {
            $this->parentContext->offsetSet($offset, $value);
        } else {
            $this->data[ $offset ] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        if ($this->parentContext->offsetExists($offset)) {
            $this->parentContext->offsetUnset($offset);
        } else {
            unset($this->data[ $offset ]);
        }
    }

    /**
     * @return mixed
     */
    public function getArrayCopy() : array
    {
        return $this->data;
    }

    /**
     * @param $functionName
     *
     * @return RuntimeFunction
     */
    public function getFunction($functionName) : RuntimeFunction
    {
        if ($this->configuration->hasFunction($functionName)) {
            return $this->configuration->getFunction($functionName);
        } else if (isset($this[ $functionName ])) {
            if (!$this[ $functionName ] instanceof RuntimeFunction) {
                if ($this[ $functionName ] === null) {
                    $this[ $functionName ] = new NullFunction();
                } else {
                    $this[ $functionName ] = RuntimeFunction::new($this[ $functionName ]);
                }
            }

            return $this[ $functionName ];
        } else {
            return $this->parentContext->getFunction($functionName);
        }
    }
}
