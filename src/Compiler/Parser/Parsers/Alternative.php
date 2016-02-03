<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class Alternative extends AbstractParser
{
    private $canSkipYield;

    public static function create(AbstractParser $first)
    {
        $alt = new Alternative();
        $alt->orA($first);

        return $alt;
    }

    /**
     * @var AbstractParser[]
     */
    private $parsers = [];

    /**
     * @var AbstractParser
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
                throw new SyntaxException(
                    "This parser can not parse the current token {$currentToken}",
                    $currentToken->getLine(),
                    $currentToken->getOffset()
                );
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

    public function orA(AbstractParser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }
}