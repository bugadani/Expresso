<?php

namespace Expresso\Compiler;

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
     * @var \SplObjectStorage
     */
    private $alternatives;
    private $defaultParser;
    private $tests;

    public function __construct(Parser $defaultParser = null)
    {
        $this->alternatives  = [];
        $this->tests         = [];
        $this->defaultParser = $defaultParser;
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
        $currentToken      = $stream->current();
        $alternativeParser = $this->defaultParser;
        foreach ($this->tests as $index => $test) {
            if ($currentToken->test($test[0], $test[1])) {
                $alternativeParser = $this->alternatives[ $index ];
                break;
            }
        }

        if ($alternativeParser !== null) {
            return $alternativeParser->parse($stream, $parser);
        }
    }
}