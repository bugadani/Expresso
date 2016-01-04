<?php

namespace Expresso\Compiler\ParserSequence;

class Container
{
    /**
     * @var Parser[]
     */
    private $parsers = [];

    public function set($parserName, Parser $parser)
    {
        $this->parsers[ $parserName ] = $parser;
    }

    public function get($parserName)
    {
        if (!isset($this->parsers[ $parserName ])) {
            throw new \OutOfBoundsException("Parser '{$parserName}' is not defined");
        }

        return $this->parsers[ $parserName ];
    }
}