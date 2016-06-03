<?php

namespace Expresso\Compiler\Compiler;

use Expresso\Compiler\Operator;
use Expresso\Compiler\OperatorCollection;
use Expresso\Runtime\RuntimeFunction;
use Expresso\Extension;

class CompilerConfiguration
{
    /**
     * @var OperatorCollection
     */
    private $binaryOperators;

    /**
     * @var OperatorCollection
     */
    private $prefixOperators;

    /**
     * @var OperatorCollection
     */
    private $unaryOperators;

    /**
     * @var OperatorCollection
     */
    private $ternaryOperators;

    /**
     * @var Operator[]
     */
    private $operators = [];

    /**
     * @var RuntimeFunction[]
     */
    private $functions = [];

    /**
     * @var string[]
     */
    private $symbols = [];

    public function __construct()
    {
        $this->binaryOperators  = new OperatorCollection();
        $this->prefixOperators  = new OperatorCollection();
        $this->unaryOperators   = new OperatorCollection();
        $this->ternaryOperators = new OperatorCollection();
    }

    public function addExtension(Extension $ext)
    {
        $this->addOperators($this->binaryOperators, $ext->getBinaryOperators());
        $this->addOperators($this->prefixOperators, $ext->getPrefixUnaryOperators());
        $this->addOperators($this->unaryOperators, $ext->getPostfixUnaryOperators());
        $this->addOperators($this->ternaryOperators, $ext->getTernaryOperators());

        $this->symbols = array_unique(array_merge($this->symbols, $ext->getSymbols()));

        foreach ($ext->getFunctions() as $functionName => $function) {
            $this->functions[ $functionName ] = $function;
        }
    }

    private function addOperators(OperatorCollection $operatorCollection, array $operators)
    {
        foreach ($operators as $symbol => $operator) {
            $this->operators[ get_class($operator) ] = $operator;
            $operatorCollection->addOperator($symbol, $operator);
        }
    }

    public function getOperatorByClass(string $class) : Operator
    {
        return $this->operators[ $class ];
    }

    public function getOperatorSymbols()
    {
        return array_merge(
            $this->binaryOperators->getSymbols(),
            $this->prefixOperators->getSymbols(),
            $this->unaryOperators->getSymbols()
        );
    }

    /**
     * @return OperatorCollection
     */
    public function getBinaryOperators() : OperatorCollection
    {
        return $this->binaryOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getPrefixOperators() : OperatorCollection
    {
        return $this->prefixOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getUnaryOperators() : OperatorCollection
    {
        return $this->unaryOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getTernaryOperators() : OperatorCollection
    {
        return $this->ternaryOperators;
    }

    public function getFunction(string $functionName) : RuntimeFunction
    {
        if (!$this->hasFunction($functionName)) {
            throw new \OutOfBoundsException("Function {$functionName} does not exist");
        }

        return $this->functions[ $functionName ];
    }

    public function getFunctions() : array
    {
        return $this->functions;
    }

    public function hasFunction(string $functionName) : bool
    {
        return isset($this->functions[ $functionName ]);
    }

    public function getSymbols() : array
    {
        return $this->symbols;
    }
}