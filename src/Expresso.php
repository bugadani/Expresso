<?php

namespace Expresso;

use Expresso\Cache\CompiledExpressionCacheInterface;
use Expresso\Cache\Parsed\NullCache as NullParsedCache;
use Expresso\Cache\Compiled\NullCache as NullCompiledCache;
use Expresso\Cache\ParsedExpressionCacheInterface;
use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\ExpressionNode;
use Expresso\Compiler\Parser\Container;
use Expresso\Compiler\Parser\GrammarParser;
use Expresso\Compiler\Tokenizer\Tokenizer;
use Expresso\Runtime\ExecutionContext;
use Recursor\Recursor;

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
     * @var GrammarParser
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

    /**
     * @var ParsedExpressionCacheInterface
     */
    private $parsedCache;

    /**
     * @var CompiledExpressionCacheInterface
     */
    private $compiledCache;

    public function __construct(ParsedExpressionCacheInterface $parsedCache = null, CompiledExpressionCacheInterface $compiledCache = null)
    {
        $this->configuration = new CompilerConfiguration();
        $this->parsedCache   = $parsedCache ?? new NullParsedCache();
        $this->compiledCache = $compiledCache ?? new NullCompiledCache();
    }

    public function addExtension(Extension $extension)
    {
        $extensionClassName = get_class($extension);
        if (!isset($this->extensions[ $extensionClassName ])) {
            $this->internalAddExtension($extensionClassName, $extension);
        }
    }

    private function internalAddExtension(string $extensionClassName, Extension $extension)
    {
        foreach ($extension->getDependencies() as $dependency) {
            if (!isset($this->extensions[ $dependency ])) {
                $this->internalAddExtension($dependency, new $dependency);
            }
        }
        $this->extensions[ $extensionClassName ] = $extension;
    }

    private function getTokenizer() : Tokenizer
    {
        if (!isset($this->tokenizer)) {
            foreach ($this->extensions as $extension) {
                $this->configuration->addExtension($extension);
            }

            $this->tokenizer = new Tokenizer(
                $this->configuration->getOperatorSymbols(),
                $this->configuration->getSymbols()
            );
        }

        return $this->tokenizer;
    }

    private function getParser() : GrammarParser
    {
        if (!isset($this->parser)) {
            $container = new Container();
            $parser    = new GrammarParser($container);
            foreach ($this->extensions as $extension) {
                $extension->addParsers($parser, $this->configuration);
            }
            $this->parser = $parser;
        }

        return $this->parser;
    }

    private function getCompiler() : Compiler
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
    private function parse(string $expression) : Node
    {
        if (!$this->parsedCache->contains($expression)) {
            $tokens = $this->getTokenizer()->tokenize($expression);

            $nodes = new ExpressionNode($expression, $this->getParser()->parse($tokens));
            $this->parsedCache->store($expression, $nodes);
        } else {
            $nodes = $this->parsedCache->retrieve($expression);
        }

        return $nodes;
    }

    /**
     * @param $expression
     *
     * @return callable
     */
    public function compile(string $expression) : callable
    {
        if (!$this->compiledCache->contains($expression)) {
            $nodes = $this->parse($expression);

            $source = $this->getCompiler()->compile($nodes);
            $function = $this->compiledCache->store($expression, $source);
        } else {
            $function = $this->compiledCache->retrieve($expression);
        }

        return function(array $args = []) use($function) {
            return $function(new ExecutionContext($args, $this->configuration));
        };
    }

    public function execute(string $expression, array $parameters)
    {
        $nodes = $this->parse($expression);

        $context  = new ExecutionContext($parameters, $this->configuration);
        $function = new Recursor([$nodes, 'evaluate']);

        return $function($context);
    }
}
