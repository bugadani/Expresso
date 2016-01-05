<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class DelegateParser extends Parser
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser, callable $callback = null)
    {
        $this->parser = $parser;
        parent::__construct($callback);
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