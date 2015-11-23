<?php

namespace Expresso;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\ExpressionNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Tokenizer;

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
     * @var Parser
     */
    private $parser;

    /**
     * @var Compiler
     */
    private $compiler;
    private $extensions = [];

    public function __construct()
    {
        $this->configuration = new CompilerConfiguration();
    }

    public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

    private function getTokenizer()
    {
        if (!isset($this->tokenizer)) {
            foreach ($this->extensions as $extension) {
                $this->configuration->addExtension($extension);
            }

            $this->tokenizer = new Tokenizer($this->configuration->getOperatorSymbols());
        }

        return $this->tokenizer;
    }

    private function getParser()
    {
        if (!isset($this->parser)) {
            $this->parser = new Parser($this->configuration);
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
     * @return NodeInterface
     */
    private function parse($expression)
    {
        $tokens = $this->getTokenizer()->tokenize($expression);

        return new ExpressionNode($expression, $this->getParser()->parse($tokens));
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
            throw new \InvalidArgumentException("Expression is not callable: $expression");
        }

        return $function;
    }

    public function execute($expression, array $parameters)
    {
        $nodes = $this->parse($expression);

        return $nodes->evaluate(new ExecutionContext($parameters));
    }
}