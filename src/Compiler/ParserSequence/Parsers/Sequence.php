<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Sequence extends Parser
{
    public function __construct(array $parsers, callable $onMatch = null)
    {
        if (empty($parsers)) {
            throw new \InvalidArgumentException('$parsers must not be empty');
        }
        foreach ($parsers as $parser) {
            $this->addStep($parser);
        }

        parent::__construct($onMatch);
    }

    /**
     * @var Parser[]
     */
    private $parsers = [];

    public function canParse(TokenStream $stream)
    {
        //A sequence can be started if the first element can parse the stream
        return $this->parsers[0]->canParse($stream);
    }

    public function parse(TokenStream $stream)
    {
        $children = [];
        foreach ($this->parsers as $parser) {
            $children[] = (yield $parser->parse($stream));
            $stream->next();
        }

        yield $this->emit($children);
    }

    private function addStep(Parser $parser)
    {
        $this->parsers[] = $parser;
    }
}