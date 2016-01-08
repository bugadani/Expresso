<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class TokenParser extends Parser
{
    /**
     * @var int
     */
    private $tokenType;

    /**
     * @var
     */
    private $test;

    /**
     * TokenParser constructor.
     *
     * @param int $tokenType
     * @param mixed $test
     */
    public function __construct($tokenType, $test = null)
    {
        $this->tokenType = $tokenType;
        $this->test      = $test;
    }

    public function canParse(TokenStream $stream)
    {
        yield $stream->current()->test($this->tokenType, $this->test);
    }

    public function parse(TokenStream $stream)
    {
        $stream->expectCurrent($this->tokenType, $this->test);
        yield $this->emit($stream->consume());
    }
}