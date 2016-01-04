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
        do {
            yield $this->getParser()->parse($stream);
            $canParse = (yield $this->canParse($stream));
        } while ($canParse);
    }
}