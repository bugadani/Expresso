<?php

namespace Expresso\Compiler;

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

        foreach ($ext->getFunctions() as $function) {
            $this->functions[ $function->getName() ] = $function;
        }
    }

    private function addOperators(OperatorCollection $operatorCollection, array $operators)
    {
        foreach ($operators as $operator) {
            $this->operators[ get_class($operator) ] = $operator;
            $operatorCollection->addOperator($operator);
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
}