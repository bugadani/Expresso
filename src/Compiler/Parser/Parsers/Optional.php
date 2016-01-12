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