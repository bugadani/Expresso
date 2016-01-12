<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Optional extends DelegateParser
{
    public static function create(Parser $parser)
    {
        return new Optional($parser);
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        if ($this->canSkipYield) {
            $canParse = $this->parser->canParse($stream->current())->current();
        } else {
            $canParse = (yield $this->parser->canParse($stream->current()));
        }

        if ($canParse) {
            if ($this->canSkipYield) {
                $child = $this->parser->parse($stream)->current();
            } else {
                $child = (yield $this->parser->parse($stream));
            }
        } else {
            $child = null;
        }

        yield $this->emit($child);
    }
}