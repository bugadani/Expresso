<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class TokenParser extends AbstractParser
{
    public static function create($tokenType, $test = null)
    {
        $tokenParser = new TokenParser();

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
        return $token->test($this->tokenType, $this->test);
    }

    public function parse(TokenStream $stream)
    {
        $stream->expect($this->tokenType, $this->test);
        //this is a hack needed to make parse a generator
        return yield $this->emit($stream->consume());
    }
}