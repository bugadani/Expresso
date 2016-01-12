<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;

class Alternative extends Parser
{
    private $canSkipYield;

    public static function create(Parser $first)
    {
        $alt = new Alternative();
        $alt->orA($first);

        return $alt;
    }

    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @var Parser
     */
    private $activeParser;

    public function canParse(Token $token)
    {
        foreach ($this->parsers as $parser) {
            $generator    = $parser->canParse($token);
            $canSkipYield = $parser instanceof TokenParser;

            if ($canSkipYield) {
                $canParse = $generator->current();
            } else {
                $canParse = (yield $generator);
            }

            if ($canParse) {
                $this->canSkipYield = $canSkipYield;
                $this->activeParser = $parser;
                yield true;
            }
        }

        $this->activeParser = null;
        $this->canSkipYield = false;

        yield false;
    }

    public function parse(TokenStream $stream)
    {
        $activeParser = $this->activeParser;
        if ($activeParser === null) {
            $currentToken = $stream->current();
            $canParse     = (yield $this->canParse($currentToken));
            if (!$canParse) {
                throw new SyntaxException("This parser can not parse the current token {$currentToken}");
            }

            $activeParser = $this->activeParser;
        }
        $this->activeParser = null;

        $parsingGenerator = $activeParser->parse($stream);
        if ($this->canSkipYield) {
            $child = $parsingGenerator->current();
        } else {
            $child = (yield $parsingGenerator);
        }
        yield $this->emit($child);
    }

    public function orA(Parser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }
}