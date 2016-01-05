<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\TokenStream;

class Any extends DelegateParser
{

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children = [];
        while (yield $this->canParse($stream)) {
            $children[] = (yield $this->getParser()->parse($stream));
            $stream->next();
        }

        yield $this->emit($children);
    }
}