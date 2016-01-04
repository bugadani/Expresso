<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class TokenParser extends Parser
{
    /**
     * @var
     */
    private $tokenType;

    /**
     * @var
     */
    private $test;

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
        /*
         * ami ezzel fura: csak a TokenParsernél kap a callback token tömböt, minden másnál Node tömböt
         *
         * Lehetséges felosztás emiatt
         * ->LeafParser (string, number, true, false, null)
         * ->NodeParser (Sequence, Alternative, Optional, AtLeastOne)
         * ->ParserReference
         */
        if ($this->callback === null) {
            yield;
        } else {
            $callback = $this->callback;
            yield $callback([$stream->current()]);
        }
    }
}