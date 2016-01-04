<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class DelegateParser extends Parser
{
    public static function create(Parser $parser)
    {
        return new static($parser);
    }

    /**
     * @var Parser
     */
    private $parser;

    protected function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function canParse(TokenStream $stream)
    {
        $childCanParse = (yield $this->getParser()->canParse($stream));

        yield $childCanParse;
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }
}