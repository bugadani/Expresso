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
     *
     * @return $this
     */
    public function process(callable $callback)
    {
        $this->emitCallback = $callback;

        return $this;
    }

    /**
     * @param AbstractParser $parser
     *
     * @return Sequence
     */
    public function followedBy(AbstractParser $parser)
    {
        return Sequence::create($this)
                       ->followedBy($parser);
    }

    /**
     * @param AbstractParser $parser
     *
     * @return Alternative
     */
    public function orA(AbstractParser $parser)
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
     * @param AbstractParser $parser
     *
     * @return RepeatSeparated
     */
    public function repeatSeparatedBy(AbstractParser $parser)
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