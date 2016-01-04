<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Container;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class ParserReference extends Parser
{

    public static function create(Container $container, $parserName)
    {
        return new ParserReference($container, $parserName);
    }

    /**
     * @var Container
     */
    private $container;
    private $parserName;

    protected function __construct(Container $container, $parserName)
    {
        $this->container  = $container;
        $this->parserName = $parserName;
    }

    public function canParse(TokenStream $stream)
    {
        $childCanParse = (yield $this->getParser()->canParse($stream));

        yield $childCanParse;
    }

    public function parse(TokenStream $stream)
    {
        $retVal = (yield $this->getParser()->parse($stream));

        yield $retVal;
    }

    /**
     * @return Parser
     */
    private function getParser()
    {
        return $this->container->get($this->parserName);
    }
}