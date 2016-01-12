<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Container;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;

class ParserReference extends Parser
{
    /**
     * @var Container
     */
    private $container;
    private $parserName;
    private $resolvedParser;

    public function __construct(Container $container, $parserName)
    {
        $this->container  = $container;
        $this->parserName = $parserName;
    }

    public function canParse(Token $token)
    {
        return $this->getParser()->canParse($token);
    }

    public function parse(TokenStream $stream)
    {
        return $this->getParser()->parse($stream);
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        if ($this->resolvedParser === null) {
            $this->resolvedParser = $this->container->get($this->parserName);
        }

        return $this->resolvedParser;
    }
}