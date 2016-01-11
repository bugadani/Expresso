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

    protected function emptyValue()
    {
        return null;
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $canParse = (yield $this->parser->canParse($stream));
        if ($canParse) {
            $child = (yield $this->parser->parse($stream));
        } else {
            $child = $this->emptyValue();
        }
        yield $this->emit($child);
    }
}