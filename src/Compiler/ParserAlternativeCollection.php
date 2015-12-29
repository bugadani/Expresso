<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Parsers\NullParser;

class ParserAlternativeCollection extends Parser
{
    public static function wrap(Parser $parser)
    {
        if (!$parser instanceof ParserAlternativeCollection) {
            $parser = new ParserAlternativeCollection($parser);
        }

        return $parser;
    }

    private $parsers = [];
    private $defaultParser;

    public function __construct(Parser $defaultParser = null)
    {
        $this->defaultParser = $defaultParser ?: new NullParser();
    }

    public function addAlternative(Parser $parser, $test)
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

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $currentToken     = $stream->current();
        $currentTokenType = $currentToken->getType();
        if (isset($this->parsers[ $currentTokenType ])) {
            foreach ($this->parsers[ $currentTokenType ] as list($tokenTest, $altParser)) {
                /** @var Parser $altParser */
                if ($currentToken->test($currentTokenType, $tokenTest)) {
                    return $altParser->parse($stream, $parser);
                }
            }
        }

        return $this->defaultParser->parse($stream, $parser);
    }
}