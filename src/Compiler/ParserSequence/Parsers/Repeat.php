<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\TokenStream;

class Repeat extends DelegateParser
{

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children = [];
        do {
            $children[] = (yield $this->getParser()->parse($stream));
        } while (yield $this->canParse($stream));

        yield $this->emit($children);
    }
}