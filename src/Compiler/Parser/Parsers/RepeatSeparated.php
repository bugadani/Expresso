<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\DelegateParser;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\TokenStream;

class RepeatSeparated extends DelegateParser
{
    public static function create(AbstractParser $parser, TokenParser $separator)
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