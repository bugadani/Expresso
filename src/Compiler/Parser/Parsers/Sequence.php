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
                    $childCanParse = (yield $parser->canParse($token));
                    if ($childCanParse) {
                        $sequence->startingParserIndex = $i;
                        yield true;
                    } else if (!$parser instanceof Optional) {
                        yield false;
                    }
                }

                yield false;
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
     * @var bool[]
     */
    private $isTokenParser = [];

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

        if ($this->startingParserIndex > 0) {
            foreach ($this->parsers as $i => $parser) {
                if ($i >= $this->startingParserIndex) {
                    if ($this->isTokenParser[ $i ]) {
                        $children[] = $parser->parse($stream)->current();
                    } else {
                        $children[] = (yield $parser->parse($stream));
                    }
                }
            }
            $this->startingParserIndex = 0;
        } else {
            foreach ($this->parsers as $i => $parser) {
                if ($this->isTokenParser[ $i ]) {
                    $children[] = $parser->parse($stream)->current();
                } else {
                    $children[] = (yield $parser->parse($stream));
                }
            }
        }

        yield $this->emit($children);
    }

    public function followedBy(AbstractParser $parser)
    {
        $this->isTokenParser[ count($this->parsers) ] = $parser instanceof TokenParser;
        $this->parsers[] = $parser;

        return $this;
    }
}