<?php

namespace Expresso\Compiler\Parser;

use Expresso\Compiler\Tokenizer\TokenStream;
use Recursor\Recursor;

class GrammarParser
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $sentence;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getSentence() : string
    {
        return $this->sentence;
    }

    /**
     * @param mixed $sentence
     */
    public function setSentence(string $sentence)
    {
        $this->sentence = $sentence;
    }

    public function parse(TokenStream $tokens)
    {
        $parser    = $this->container->get($this->sentence);
        $generator = new Recursor([$parser, 'parse']);

        return $generator($tokens);
    }

    public function getContainer() : Container
    {
        return $this->container;
    }
}