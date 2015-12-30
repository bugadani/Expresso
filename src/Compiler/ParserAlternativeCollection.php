<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Parsers\NullParser;

class ParserAlternativeCollection extends SubParser
{
    public static function wrap(SubParser $parser)
    {
        if (!$parser instanceof ParserAlternativeCollection) {
            $parser = new ParserAlternativeCollection($parser);
        }

        return $parser;
    }

    private $parsers = [];
    private $defaultParser;

    public function __construct(SubParser $defaultParser = null)
    {
        $this->defaultParser = $defaultParser ?: new NullParser();
    }

    public function addAlternative(SubParser $parser, $test)
    {
        $tokenTest = null;
        if (is_array($test)) {
            $tokenType = $test[0];
            if (isset($test[1])) {
                $tokenTest = $test[1];
            }
        } else {
            $tokenType = $test;
        }

        if (!isset($this->parsers[ $tokenType ])) {
            $this->parsers[ $tokenType ] = [];
        }
        $this->parsers[ $tokenType ][] = [$tokenTest, $parser];
    }

    public function parse(TokenStream $stream, Parser $parser)
    {
        $currentToken     = $stream->current();
        $currentTokenType = $currentToken->getType();
        if (isset($this->parsers[ $currentTokenType ])) {
            foreach ($this->parsers[ $currentTokenType ] as list($tokenTest, $altParser)) {
                /** @var SubParser $altParser */
                if ($currentToken->test($currentTokenType, $tokenTest)) {
                    return $altParser->parse($stream, $parser);
                }
            }
        }

        return $this->defaultParser->parse($stream, $parser);
    }
}