<?php

namespace Expresso\Compiler\Parser;

use Expresso\Compiler\Parser\Parsers\Alternative;
use Expresso\Compiler\Parser\Parsers\Optional;
use Expresso\Compiler\Parser\Parsers\Repeat;
use Expresso\Compiler\Parser\Parsers\RepeatSeparated;
use Expresso\Compiler\Parser\Parsers\Sequence;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

abstract class AbstractParser
{
    /**
     * @var callable
     */
    private $emitCallback;

    /**
     * @var AbstractParser
     */
    private $parent;

    /**
     * @param Token $token
     *
     * @return \Generator
     */
    abstract public function canParse(Token $token);

    /**
     * @param TokenStream $stream
     *
     * @return \Generator
     */
    abstract public function parse(TokenStream $stream);

    public function setParent(AbstractParser $parser = null)
    {
        $this->parent = $parser;
    }

    /**
     * @return AbstractParser
     */
    public function getParent()
    {
        return $this->parent;
    }

    protected function emit($data)
    {
        if ($this->emitCallback !== null) {
            $callback = $this->emitCallback;

            $data = $callback($data, $this->parent);
        }

        return $data;
    }

    /**
     * @param callable $callback
     * @return AbstractParser
     */
    public function process(callable $callback) : AbstractParser
    {
        $this->emitCallback = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     * @return AbstractParser
     */
    public function overrideProcess(callable $callback) : AbstractParser
    {
        $oldCallback        = $this->emitCallback;
        $this->emitCallback = function (...$args) use ($callback, $oldCallback) {
            $this->emitCallback = $oldCallback;

            return $callback(...$args);
        };

        return $this;
    }

    /**
     * @param AbstractParser $parser
     *
     * @return Sequence
     */
    public function followedBy(AbstractParser $parser) : Sequence
    {
        return Sequence::create($this)
                       ->followedBy($parser);
    }

    /**
     * @param AbstractParser $parser
     *
     * @return Alternative
     */
    public function orA(AbstractParser $parser) : Alternative
    {
        return Alternative::create($this)
                          ->orA($parser);
    }

    /**
     * @return Repeat
     */
    public function repeated() : Repeat
    {
        return Repeat::create($this);
    }

    /**
     * @param AbstractParser $parser
     *
     * @return RepeatSeparated
     */
    public function repeatSeparatedBy(AbstractParser $parser) : RepeatSeparated
    {
        return RepeatSeparated::create($this, $parser);
    }

    /**
     * @return Optional
     */
    public function optional() : Optional
    {
        return Optional::create($this);
    }
}