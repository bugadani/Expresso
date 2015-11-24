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
    private $unaryPrefixOperators;

    /**
     * @var OperatorCollection
     */
    private $unaryPostfixOperators;

    /**
     * @var ParserCollection
     */
    private $parserCollection;

    public function __construct()
    {
        $this->binaryOperators       = new OperatorCollection();
        $this->unaryPrefixOperators  = new OperatorCollection();
        $this->unaryPostfixOperators = new OperatorCollection();
        $this->parserCollection      = new ParserCollection();
    }

    public function addExtension(Extension $ext)
    {
        $this->binaryOperators->addOperators($ext->getBinaryOperators());
        $this->unaryPrefixOperators->addOperators($ext->getPrefixUnaryOperators());
        $this->unaryPostfixOperators->addOperators($ext->getPostfixUnaryOperators());

        $ext->addParsers($this->parserCollection);
    }

    public function getOperatorSymbols()
    {
        return array_merge(
            $this->binaryOperators->getSymbols(),
            $this->unaryPrefixOperators->getSymbols(),
            $this->unaryPostfixOperators->getSymbols()
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
    public function getUnaryPrefixOperators()
    {
        return $this->unaryPrefixOperators;
    }

    /**
     * @return OperatorCollection
     */
    public function getUnaryPostfixOperators()
    {
        return $this->unaryPostfixOperators;
    }

    /**
     * @return ParserCollection
     */
    public function getParserCollection()
    {
        return $this->parserCollection;
    }
}