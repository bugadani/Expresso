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
     * @var ExpressionFunction[]
     */
    private $functions;

    public function __construct()
    {
        $this->binaryOperators = new OperatorCollection();
        $this->prefixOperators = new OperatorCollection();
        $this->unaryOperators  = new OperatorCollection();
        $this->functions       = new \ArrayObject();
    }

    public function addExtension(Extension $ext)
    {
        $this->binaryOperators->addOperators($ext->getBinaryOperators());
        $this->prefixOperators->addOperators($ext->getPrefixUnaryOperators());
        $this->unaryOperators->addOperators($ext->getPostfixUnaryOperators());

        foreach ($ext->getFunctions() as $function) {
            $this->functions[ $function->getName() ] = $function;
        }
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

    public function getFunctions()
    {
        return $this->functions;
    }
}