<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class Parser
{
    protected $callback;

    public static function create()
    {
        return new static();
    }

    protected function __construct()
    {
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    abstract public function canParse(TokenStream $stream);

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    abstract public function parse(TokenStream $stream);

    public function onMatch(callable $callback)
    {
        $this->callback = $callback;
    }
}