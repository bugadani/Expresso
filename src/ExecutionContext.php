<?php

namespace Expresso;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\RuntimeFunction;

class ExecutionContext implements \ArrayAccess
{

    public static function &access(&$where, $what)
    {
        if (is_array($where)) {
            return $where[ $what ];
        } else if (is_object($where)) {
            if (method_exists($where, $what)) {
                $methodWrapper = new RuntimeFunction([$where, $what]);

                return $methodWrapper;
            } else if ($where instanceof \ArrayAccess) {
                return $where[ $what ];
            } else {
                return $where->{$what};
            }
        }

        throw new \OutOfBoundsException("{$what} is not present in \$where");
    }

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
        $this->parentContext = $parentContext ?? new class extends ExecutionContext
            {
                public function __construct()
                {

                }

                public function &offsetGet($index)
                {
                    throw new \OutOfBoundsException("Array index out of bounds: {$index}");
                }

                public function offsetExists($offset)
                {
                    return false;
                }

                public function getFunction($functionName) : RuntimeFunction
                {
                    throw new \OutOfBoundsException('Function not found: ' . $functionName);
                }
            };
    }

    public function createInnerScope($input)
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
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data) || $this->parentContext->offsetExists($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
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
    public function getArrayCopy()
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
        $functions = $this->configuration->getFunctions();

        if (isset($functions[ $functionName ])) {
            return $functions[ $functionName ];
        }

        if (isset($this[ $functionName ])) {
            return new RuntimeFunction($this[ $functionName ]);
        }

        return $this->parentContext->getFunction($functionName);
    }
}
