<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class DelegateParser extends Parser
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function canParse(TokenStream $stream)
    {
        return $this->getParser()->canParse($stream);
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }
}