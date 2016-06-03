<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class Sequence extends AbstractParser
{
    public static function create(AbstractParser $first)
    {
        $sequence = new Sequence();

        if ($first instanceof Optional) {
            $sequence->canParseCallback = function (Token $token) use ($sequence) {
                //A sequence can be started if the first element can parse the stream - optionals may be skipped
                foreach ($sequence->parsers as $i => $parser) {
                    if (yield $parser->canParse($token)) {
                        $sequence->startingParserIndex = $i;
                        return true;
                    } else if (!$parser instanceof Optional) {
                        return false;
                    }
                }

                return false;
            };
        } else {
            $sequence->canParseCallback = [$first, 'canParse'];
        }

        $sequence->followedBy($first);

        return $sequence;
    }

    /**
     * @var callable|null
     */
    private $runBefore;

    /**
     * The number of parsers to skip
     *
     * @var int
     */
    private $startingParserIndex = 0;

    /**
     * @var AbstractParser[]
     */
    private $parsers = [];

    /**
     * @var callable
     */
    private $canParseCallback;

    public function runBefore(callable $callback)
    {
        $this->runBefore = $callback;

        return $this;
    }

    public function canParse(Token $token)
    {
        $canParseCallback = $this->canParseCallback;

        return $canParseCallback($token);
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

        return $this->emit($children);
    }

    public function followedBy(AbstractParser $parser) : Sequence
    {
        $parser->setParent($this);
        $this->parsers[] = $parser;

        return $this;
    }
}