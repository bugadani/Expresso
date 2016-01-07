<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Container;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class ParserReference extends Parser
{
    /**
     * @var Container
     */
    private $container;
    private $parserName;

    public function __construct(Container $container, $parserName, callable $callback = null)
    {
        $this->container  = $container;
        $this->parserName = $parserName;

        parent::__construct($callback);
    }

    public function canParse(TokenStream $stream)
    {
        return $this->getParser()->canParse($stream);
    }

    public function parse(TokenStream $stream)
    {
        return $this->getParser()->parse($stream);
    }

    /**
     * @return Parser
     */
    private function getParser()
    {
        return $this->container->get($this->parserName);
    }
}