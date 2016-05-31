<?php

namespace Expresso\Compiler\Parser;

use Expresso\Compiler\Tokenizer\Token;

abstract class DelegateParser extends AbstractParser
{
    /**
     * @var AbstractParser
     */
    protected $parser;

    public function __construct(AbstractParser $parser)
    {
        $this->parser = $parser;
    }

    public function setParent(AbstractParser $parser = null)
    {
        $this->getParser()->setParent($parser);
    }

    public function getParent()
    {
        return $this->getParser()->getParent();
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