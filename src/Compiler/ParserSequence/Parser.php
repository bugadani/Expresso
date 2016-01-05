<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class Parser
{
    /**
     * @var callable
     */
    private $emitCallback;

    public function __construct(callable $callback = null) {

        $this->emitCallback = $callback;
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    abstract public function canParse(TokenStream $stream);

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    abstract public function parse(TokenStream $stream);

    protected function emit($data)
    {
        if ($this->emitCallback === null) {
            return $data;
        } else {
            $callback = $this->emitCallback;
            return $callback($data);
        }
    }
}