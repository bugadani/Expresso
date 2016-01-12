<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\Token;
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
    private $startingParserIndex = 0;

    /**
     * @var Parser[]
     */
    private $parsers = [];

    public function runBefore(callable $callback)
    {
        $this->runBefore = $callback;

        return $this;
    }

    public function canParse(Token $token)
    {
        //A sequence can be started if the first element can parse the stream - optionals may be skipped
        foreach ($this->parsers as $i => $parser) {
            $childCanParse = (yield $parser->canParse($token));
            if ($childCanParse) {
                $this->startingParserIndex = $i;
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
        foreach ($this->parsers as $i => $parser) {
            if ($i >= $this->startingParserIndex) {
                $children[] = (yield $parser->parse($stream));
            }
        }
        $this->startingParserIndex = 0;
        yield $this->emit($children);
    }

    public function followedBy(Parser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }
}