<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\Alternative;
use Expresso\Compiler\ParserSequence\Parsers\Optional;
use Expresso\Compiler\ParserSequence\Parsers\Repeat;
use Expresso\Compiler\ParserSequence\Parsers\RepeatSeparated;
use Expresso\Compiler\ParserSequence\Parsers\Sequence;
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

    /**
     * @param Parser $parser
     * @return Sequence
     */
    public function followedBy(Parser $parser)
    {
        return Sequence::create($this)
                       ->followedBy($parser);
    }

    /**
     * @param Parser $parser
     * @return Alternative
     */
    public function orA(Parser $parser)
    {
        return Alternative::create($this)
                          ->orA($parser);
    }

    /**
     * @return Repeat
     */
    public function repeated()
    {
        return Repeat::create($this);
    }

    /**
     * @param Parser $parser
     * @return RepeatSeparated
     */
    public function repeatSeparatedBy(Parser $parser)
    {
        return RepeatSeparated::create($this, $parser);
    }

    /**
     * @return Optional
     */
    public function optional()
    {
        return Optional::create($this);
    }
}