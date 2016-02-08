<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\DelegateParser;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\TokenStream;

class RepeatSeparated extends DelegateParser
{
    public static function create(AbstractParser $parser, AbstractParser $separator)
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
     *
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        $children = [];

        $children[] = (yield $this->parser->parse($stream));
        while (yield $this->separator->canParse($stream->current())) {
            yield $this->separator->parse($stream);
            $children[] = (yield $this->parser->parse($stream));
        }

        yield $this->emit($children);
    }
}