<?php

namespace Expresso\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\Container;
use Expresso\Compiler\Parser\AbstractParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class ParserReference extends AbstractParser
{
    /**
     * @var Container
     */
    private $container;
    private $parserName;

    /**
     * @var AbstractParser
     */
    private $resolvedParser;
    private $parentToSet;

    public function __construct(Container $container, $parserName)
    {
        $this->container  = $container;
        $this->parserName = $parserName;
    }

    public function canParse(Token $token)
    {
        return $this->getParser()->canParse($token);
    }

    public function parse(TokenStream $stream)
    {
        return $this->getParser()->parse($stream);
    }

    public function setParent(AbstractParser $parser = null)
    {
        if ($this->resolvedParser === null) {
            $this->parentToSet = $parser;
        } else {
            $this->getParser()->setParent($parser);
        }
    }

    public function getParent()
    {
        return $this->getParser()->getParent();
    }

    /**
     * @return AbstractParser
     */
    public function getParser() : AbstractParser
    {
        if ($this->resolvedParser === null) {
            $this->resolvedParser = $this->container->get($this->parserName);
            $this->resolvedParser->setParent($this->parentToSet);
        }

        return $this->resolvedParser;
    }
}