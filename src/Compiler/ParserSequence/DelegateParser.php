<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;

abstract class DelegateParser extends Parser
{
    /**
     * @var Parser
     */
    protected $parser;
    protected $canSkipYield;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
        $this->canSkipYield = $parser instanceof TokenParser;
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