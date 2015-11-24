<?php

namespace Expresso\Compiler;

class ParserCollection
{
    private $parsers = [];
    private $tokenParsers = [];
    private $defaultParser;

    public function add($id, Parser $parser, $token = null)
    {
        $this->parsers[ $id ] = $parser;
        if ($token !== null) {
            foreach ((array)$token as $t) {
                $this->tokenParsers[ $t ] = $parser;
            }
        }
    }

    public function setDefaultParser($defaultParserId)
    {
        $this->defaultParser = $this->get($defaultParserId);
    }

    /**
     * @param $id
     * @return Parser
     */
    public function get($id)
    {
        return $this->parsers[ $id ];
    }

    /**
     * @param $token
     * @return Parser
     */
    public function getTokenParser($token)
    {
        if (isset($this->tokenParsers[ $token ])) {
            return $this->tokenParsers[ $token ];
        } else if (isset($this->defaultParser)) {
            return $this->defaultParser;
        }

        throw new \OutOfBoundsException("No parser is set for token {$token}");
    }
}