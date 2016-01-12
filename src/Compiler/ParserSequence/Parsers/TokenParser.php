<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;

class TokenParser extends Parser
{
    public static function create($tokenType, $test = null)
    {
        $tokenParser = new TokenParser($tokenType, $test);

        $tokenParser->tokenType = $tokenType;
        $tokenParser->test      = $test;

        return $tokenParser;
    }

    /**
     * @var int
     */
    private $tokenType;

    /**
     * @var
     */
    private $test;

    public function canParse(Token $token)
    {
        yield $token->test($this->tokenType, $this->test);
    }

    public function parse(TokenStream $stream)
    {
        $stream->expect($this->tokenType, $this->test);
        yield $this->emit($stream->consume());
    }
}