<?php

namespace Expresso\Compiler;

class ParserAlternativeCollection extends Parser
{
    /**
     * @var \SplObjectStorage
     */
    private $alternatives;
    private $defaultParser;
    private $tests;

    public function __construct(Parser $defaultParser = null)
    {
        $this->alternatives    = [];
        $this->tests           = [];
        $this->repetitionModes = [];
        $this->defaultParser   = $defaultParser;
    }

    public function addAlternative(Parser $parser, $test)
    {
        $this->alternatives[] = $parser;
        $this->tests[]        = (array)$test;
    }

    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        foreach ($this->tests as $index => $test) {

            $tokenType = $test[0];
            $tokenValue = isset($test[1]) ? $test[1] : null;

            if ($currentToken->test($tokenType, $tokenValue)) {
                $this->alternatives[ $index ]->parse($currentToken, $stream, $parser);

                return;
            }
        }

        if ($this->defaultParser !== null) {
            $this->defaultParser->parse($currentToken, $stream, $parser);
        }
    }
}