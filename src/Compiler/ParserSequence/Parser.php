<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\TokenStream;

abstract class Parser
{
    /**
     * @var callable
     */
    private $emitCallback;

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

    /**
     * @param callable $callback
     * @return $this
     */
    public function process(callable $callback)
    {
        $this->emitCallback = $callback;

        return $this;
    }
}