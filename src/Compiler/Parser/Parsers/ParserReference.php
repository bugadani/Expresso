<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\Container;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class ParserReference extends AbstractParser
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
     * @return AbstractParser
     */
    public function getParser()
    {
        if ($this->resolvedParser === null) {
            $this->resolvedParser = $this->container->get($this->parserName);
        }

        return $this->resolvedParser;
    }
}