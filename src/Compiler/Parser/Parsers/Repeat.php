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
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children = [];
        if ($this->canSkipYield) {

            do {
                $children[] = $this->parser->parse($stream)->current();
            } while ($this->parser->canParse($stream->current())->current());

        } else {

            do {
                $children[] = (yield $this->parser->parse($stream));
            } while (yield $this->parser->canParse($stream->current()));

        }
        yield $this->emit($children);

    }
}