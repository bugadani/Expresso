<?php

namespace Expresso;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Parser\OperatorParser;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\ExpressionNode;
use Expresso\Compiler\Tokenizer\Tokenizer;

class Expresso
{
    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * @var OperatorParser
     */
    private $parser;

    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var Extension[]
     */
    private $extensions = [];

    public function __construct()
    {
        $this->configuration = new CompilerConfiguration();
    }

    public function addExtension(Extension $extension)
    {
        $extensionClassName = get_class($extension);
        if (!isset($this->extensions[ $extensionClassName ])) {
            $this->internalAddExtension($extensionClassName, $extension);
        }
    }

    private function internalAddExtension($extensionClassName, Extension $extension)
    {
        $this->extensions[ $extensionClassName ] = $extension;

        foreach ($extension->getDependencies() as $dependency) {
            if (!isset($this->extensions[ $dependency ])) {
                $this->internalAddExtension($dependency, new $dependency);
            }
        }
    }

    private function getTokenizer()
    {
        if (!isset($this->tokenizer)) {
            foreach ($this->extensions as $extension) {
                $this->configuration->addExtension($extension);
            }

            $this->tokenizer = new Tokenizer($this->configuration->getOperatorSymbols(), $this->configuration->getSymbols());
        }

        return $this->tokenizer;
    }

    private function getParser()
    {
        if (!isset($this->parser)) {
            $parser = new OperatorParser($this->configuration);
            foreach ($this->extensions as $extension) {
                $extension->addParsers($parser, $this->configuration);
            }
            $this->parser = $parser;
        }

        return $this->parser;
    }

    private function getCompiler()
    {
        if (!isset($this->compiler)) {
            $this->compiler = new Compiler($this->configuration);
        }

        return $this->compiler;
    }

    /**
     * @param $expression
     *
     * @return Node
     */
    private function parse($expression)
    {
        $tokens = $this->getTokenizer()->tokenize($expression);

        return new ExpressionNode($expression, $this->getParser()->parseTokenStream($tokens));
    }

    /**
     * @param $expression
     *
     * @return callable
     */
    public function compile($expression)
    {
        $nodes = $this->parse($expression);

        $source = $this->getCompiler()->compile($nodes);

        $function = eval('return ' . $source);

        if (!is_callable($function)) {
            throw new \InvalidArgumentException("Expression is not callable: {$expression}, compiled: {$source}");
        }

        return $function;
    }

    public function execute($expression, array $parameters)
    {
        $nodes = $this->parse($expression);

        $context = new EvaluationContext($parameters, $this->configuration);

        return GeneratorHelper::executeGeneratorsRecursive(
            $nodes->evaluate($context)
        );
    }
}
