<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\TokenStream;

class Optional extends DelegateParser
{

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function canParse(TokenStream $stream)
    {
        yield true;
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $inner    = $this->getParser();
        $canParse = (yield $inner->canParse($stream));
        if ($canParse) {
            $child = (yield $inner->parse($stream));
        } else {
            $child = null;
        }

        yield $this->emit($child);
    }
}