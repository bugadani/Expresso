<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Repeat extends DelegateParser
{
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
        $children = [];
        $parser = $this->getParser();

        if ($this->separator === null) {
            do {
                $children[] = (yield $parser->parse($stream));
            } while (yield $parser->canParse($stream));
        } else {
            $children[] = (yield $parser->parse($stream));

            while (yield $this->separator->canParse($stream)) {
                yield $this->separator->parse($stream);
                $children[] = (yield $parser->parse($stream));
            }
        }

        yield $this->emit($children);
    }

    public function separatedBy(Parser $parser)
    {
        $this->separator = $parser;

        return $this;
    }
}