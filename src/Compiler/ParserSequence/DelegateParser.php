<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\Token;

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

    public function canParse(Token $token)
    {
        return $this->parser->canParse($token);
    }

    public function getParser()
    {
        return $this->parser;
    }
}