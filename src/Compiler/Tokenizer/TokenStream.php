<?php

namespace Expresso\Compiler\Tokenizer;

use Expresso\Compiler\Exceptions\SyntaxException;

class TokenStream
{
    /**
     * @var \Iterator
     */
    private $tokens;

    /**
     * @var Token
     */
    private $current;

    public function __construct(\Iterator $tokens)
    {
        $this->tokens = $tokens;
        $tokens->rewind();
        $this->current = $tokens->current();
    }

    public function consume() : Token
    {
        $current = $this->current;
        $this->tokens->next();
        $this->current = $this->tokens->current();

        return $current;
    }

    /**
     * @return Token
     */
    public function current() : Token
    {
        return $this->current;
    }

    /**
     * @param      $type
     * @param null $value
     *
     * @return Token
     * @throws SyntaxException
     */
    public function expect($type, $value = null) : Token
    {
        if ($this->current->test($type, $value)) {
            return $this->current;
        }
        $expectation = new Token($type, $value);
        throw new SyntaxException(
            "Unexpected {$this->current}, expected {$expectation}",
            $this->current->getLine(),
            $this->current->getOffset()
        );
    }
}
