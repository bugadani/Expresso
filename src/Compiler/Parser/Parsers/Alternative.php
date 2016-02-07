<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class Alternative extends AbstractParser
{
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
            if (yield $parser->canParse($token)) {
                $this->activeParser = $parser;
                yield true;
            }
        }

        $this->activeParser = null;

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
        $child            = (yield $parsingGenerator);
        yield $this->emit($child);
    }

    public function orA(AbstractParser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }
}