<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class RepeatSeparated extends DelegateParser
{
    public static function create(Parser $parser, TokenParser $separator)
    {
        $repeat            = new static($parser);
        $repeat->separator = $separator;

        return $repeat;
    }

    /**
     * @var TokenParser
     */
    private $separator;

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children = [];

        if ($this->canSkipYield) {

            $children[] = $this->parser->parse($stream)->current();
            while ($this->separator->canParse($stream->current())->current()) {
                //$this->separator is a TokenParser, so parse() is a generator. Let's run it directly
                $this->separator->parse($stream)->current();
                $children[] = $this->parser->parse($stream)->current();
            }

        } else {

            $children[] = (yield $this->parser->parse($stream));
            while ($this->separator->canParse($stream->current())->current()) {
                //$this->separator is a TokenParser, so parse() is a generator. Let's run it directly
                $this->separator->parse($stream)->current();
                $children[] = (yield $this->parser->parse($stream));
            }

        }

        yield $this->emit($children);
    }
}