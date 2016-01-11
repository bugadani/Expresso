<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Sequence extends Parser
{
    public static function create(Parser $first)
    {
        $sequence = new Sequence();
        $sequence->followedBy($first);

        return $sequence;
    }

    private $runBefore;

    /**
     * @var Parser[]
     */
    private $parsers = [];

    public function runBefore(callable $callback)
    {
        $this->runBefore = $callback;

        return $this;
    }

    public function canParse(TokenStream $stream)
    {
        //A sequence can be started if the first element can parse the stream - optionals may be skipped
        foreach ($this->parsers as $parser) {
            $childCanParse = (yield $parser->canParse($stream));
            if ($childCanParse) {
                yield true;
            } else if (!$parser instanceof Optional) {
                yield false;
            }
        }

        yield false;
    }

    public function parse(TokenStream $stream)
    {
        if ($this->runBefore !== null) {
            $callback = $this->runBefore;
            $callback();
        }
        $children = [];
        foreach ($this->parsers as $parser) {
            $children[] = (yield $parser->parse($stream));
        }

        yield $this->emit($children);
    }

    public function followedBy(Parser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }
}