<?php

namespace Expresso\Compiler\Compiler;

use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\OperatorCollection;
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
     * @var \ArrayObject
     */
    private $operators;

    /**
     * @var ExpressionFunction[]
     */
    private $functions;

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
        $this->functions        = new \ArrayObject();
        $this->operators        = new \ArrayObject();
    }

    public function addExtension(Extension $ext)
    {
        $this->addOperators($this->binaryOperators, $ext->getBinaryOperators());
        $this->addOperators($this->prefixOperators, $ext->getPrefixUnaryOperators());
        $this->addOperators($this->unaryOperators, $ext->getPostfixUnaryOperators());
        $this->addOperators($this->ternaryOperators, $ext->getTernaryOperators());

        $this->symbols = array_unique(array_merge($this->symbols, $ext->getSymbols()));

        foreach ($ext->getFunctions() as $function) {
            $this->functions[ $function->getName() ] = $function;
        }
    }

    private function addOperators(OperatorCollection $operatorCollection, array $operators)
    {
        foreach ($operators as $symbol => $operator) {
            $this->operators[ get_class($operator) ] = $operator;
            $operatorCollection->addOperator($symbol, $operator);
        }
    }

    public function getOperatorByClass($class)
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
    public function getBinaryOperators()
    {
        return $this->binaryOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getPrefixOperators()
    {
        return $this->prefixOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getUnaryOperators()
    {
        return $this->unaryOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getTernaryOperators()
    {
        return $this->ternaryOperators;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    public function getSymbols()
    {
        return $this->symbols;
    }
}