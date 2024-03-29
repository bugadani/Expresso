<?php

namespace Expresso\Compiler\Parser;

class Container
{
    /**
     * @var AbstractParser[]
     */
    private $parsers = [];

    /**
     * @param $parserName
     * @param AbstractParser $parser
     *
     * @return AbstractParser
     */
    public function set(string $parserName, AbstractParser $parser) : AbstractParser
    {
        $this->parsers[ $parserName ] = $parser;

        return $parser;
    }

    public function get(string $parserName) : AbstractParser
    {
        if (!isset($this->parsers[ $parserName ])) {
            throw new \OutOfBoundsException("Parser '{$parserName}' is not defined");
        }

        return $this->parsers[ $parserName ];
    }
}