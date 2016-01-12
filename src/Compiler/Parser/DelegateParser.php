<?php

namespace Expresso\Compiler\Parser;

use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;

abstract class DelegateParser extends AbstractParser
{
    /**
     * @var AbstractParser
     */
    protected $parser;
    protected $canSkipYield;

    public function __construct(AbstractParser $parser)
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