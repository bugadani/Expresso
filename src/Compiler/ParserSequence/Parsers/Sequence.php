<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Sequence extends Parser
{
    public static function create(array $parsers)
    {
        if (empty($parsers)) {
            throw new \InvalidArgumentException('$parsers must not be empty');
        }
        $sequence = new Sequence();
        foreach ($parsers as $parser) {
            $sequence->addStep($parser);
        }

        return $sequence;
    }

    /**
     * @var Parser[]
     */
    private $parsers = [];

    public function canParse(TokenStream $stream)
    {
        //A sequence can be started if the first element can parse the stream
        $childCanParse = (yield $this->parsers[0]->canParse($stream));

        yield $childCanParse;
    }

    public function parse(TokenStream $stream)
    {
        $children = [];
        foreach ($this->parsers as $parser) {
            $children[] = (yield $parser->parse($stream));
            $stream->next();
        }
    }

    private function addStep(Parser $parser)
    {
        $this->parsers[] = $parser;
    }
}