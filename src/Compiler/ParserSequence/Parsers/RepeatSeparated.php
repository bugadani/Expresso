<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class RepeatSeparated extends DelegateParser
{
    public static function create(Parser $parser, Parser $separator)
    {
        $repeat            = new static($parser);
        $repeat->separator = $separator;

        return $repeat;
    }

    /**
     * @var Parser
     */
    private $separator;

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children   = [];
        $children[] = (yield $this->parser->parse($stream));

        while (yield $this->separator->canParse($stream)) {
            yield $this->separator->parse($stream);
            $children[] = (yield $this->parser->parse($stream));
        }

        yield $this->emit($children);
    }
}