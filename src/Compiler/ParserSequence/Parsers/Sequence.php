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
        //A sequence can be started if the first element can parse the stream - optionals may be skipped
        for ($firstNonOptional = 0; isset($this->parsers[ $firstNonOptional ]); $firstNonOptional++) {
            if ($this->parsers[ $firstNonOptional ] instanceof Optional) {
                $optionalCanParse = (yield $this->parsers[ $firstNonOptional ]->canParse($stream));
                if ($optionalCanParse) {
                    yield true;
                }
            } else {
                yield (yield $this->parsers[ $firstNonOptional ]->canParse($stream));
            }
        }

        yield false;
    }

    public function parse(TokenStream $stream)
    {
        $children = [];
        foreach ($this->parsers as $parser) {
            $children[] = (yield $parser->parse($stream));
        }

        yield $this->emit($children);
    }

    private function addStep(Parser $parser)
    {
        $this->parsers[] = $parser;
    }
}