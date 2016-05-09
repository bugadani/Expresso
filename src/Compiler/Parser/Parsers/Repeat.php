<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\DelegateParser;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\TokenStream;

class Repeat extends DelegateParser
{
    public static function create(AbstractParser $parser)
    {
        return new Repeat($parser);
    }

    /**
     * @param TokenStream $stream
     *
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children = [];

        do {
            $children[] = (yield $this->parser->parse($stream));
        } while (yield $this->parser->canParse($stream->current()));

        return $this->emit($children);

    }
}