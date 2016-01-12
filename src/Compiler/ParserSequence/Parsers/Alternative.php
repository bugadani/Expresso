<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;

class Alternative extends Parser
{
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
        $this->activeParser = null;
        foreach ($this->parsers as $parser) {
            $canParse = (yield $parser->canParse($token));
            if ($canParse) {
                $this->activeParser = $parser;

                yield true;
            }
        }

        yield false;
    }

    public function parse(TokenStream $stream)
    {
        $activeParser       = $this->activeParser;
        $this->activeParser = null;

        if ($activeParser === null) {
            $currentToken = $stream->current();
            foreach ($this->parsers as $parser) {
                if (yield $parser->canParse($currentToken)) {
                    $activeParser = $parser;
                    break;
                }
            }

            if ($activeParser === null) {
                throw new SyntaxException("This parser can not parse the current token {$currentToken}");
            }
        }
        $child = (yield $activeParser->parse($stream));

        yield $this->emit($child);
    }

    public function orA(Parser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }
}