<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\TokenStream;

class AtLeastOne extends DelegateParser
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
            $canParse   = (yield $this->canParse($stream));
        } while ($canParse);
        /*
         * Akkor ennek a kimenete most egy 1+ méretű tömb, ha illeszkedik
         */
        yield $children;
    }
}