<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class DelegateParser extends Parser
{
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function canParse(TokenStream $stream)
    {
        return $this->parser->canParse($stream);
    }

    public function getParser()
    {
        return $this->parser;
    }
}