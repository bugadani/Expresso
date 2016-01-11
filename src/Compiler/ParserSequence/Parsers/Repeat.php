<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Repeat extends DelegateParser
{
    public static function create(Parser $parser)
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

        do {
            $children[] = (yield $this->parser->parse($stream));
        } while (yield $this->parser->canParse($stream));

        yield $this->emit($children);
    }

    public function separatedBy(Parser $parser)
    {
        return RepeatSeparated::create($this->parser, $parser);
    }
}