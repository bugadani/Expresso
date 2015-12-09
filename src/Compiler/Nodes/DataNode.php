<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class DataNode extends Node
{
    private static $nullInstance;

    public static function nullNode()
    {
        if (self::$nullInstance === null) {
            self::$nullInstance = new DataNode(null);
        }

        return self::$nullInstance;
    }

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addData($this->value);
    }

    public function evaluate(EvaluationContext $context, array $childResults)
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}