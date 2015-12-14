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

    /**
     * @var Parser[]
     */
    private $alternatives = [];
    private $tests = [];
    private $defaultParser;

    public function __construct(Parser $defaultParser = null)
    {
        $this->defaultParser = $defaultParser ?: new NullParser();
    }

    public function addAlternative(Parser $parser, $test)
    {
        $this->alternatives[] = $parser;

        $test = (array)$test;
        if (!isset($test[1])) {
            $test[1] = null;
        }
        $this->tests[] = $test;
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $currentToken = $stream->current();
        foreach ($this->tests as $index => $test) {
            if ($currentToken->test($test[0], $test[1])) {
                return $this->alternatives[ $index ]->parse($stream, $parser);
            }
        }

        return $this->defaultParser->parse($stream, $parser);
    }
}