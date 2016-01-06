<?php

namespace Expresso\Compiler\ParserSequence;

class Container
{
    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @param $parserName
     * @param Parser $parser
     * @return Parser
     */
    public function set($parserName, Parser $parser)
    {
        $this->parsers[ $parserName ] = $parser;

        return $parser;
    }

    public function get($parserName)
    {
        if (!isset($this->parsers[ $parserName ])) {
            throw new \OutOfBoundsException("Parser '{$parserName}' is not defined");
        }

        return $this->parsers[ $parserName ];
    }
}