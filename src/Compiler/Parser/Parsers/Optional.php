<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\DelegateParser;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\TokenStream;

class Optional extends DelegateParser
{
    public static function create(AbstractParser $parser)
    {
        return new Optional($parser);
    }

    /**
     * @param TokenStream $stream
     *
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        if (yield $this->parser->canParse($stream->current())) {
            $child = (yield $this->parser->parse($stream));
        } else {
            $child = null;
        }

        return $this->emit($child);
    }
}