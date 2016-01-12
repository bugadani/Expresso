<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Exceptions\SyntaxException;

class TokenStream
{
    /**
     * @var \Iterator
     */
    private $tokens;

    public function __construct(\Iterator $tokens)
    {
        $this->tokens = $tokens;
        $tokens->rewind();
        $this->current = $tokens->current();
    }

    public function consume()
    {
        $current = $this->current;
        $this->tokens->next();
        $this->current = $this->tokens->current();

        return $current;
    }

    /**
     * @return Token
     */
    public function current()
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
    public function expect($type, $value = null)
    {
        if ($this->current->test($type, $value)) {
            return $this->current;
        }
        $expectation = new Token($type, $value);
        throw new SyntaxException("Unexpected {$this->current}, expected {$expectation}");
    }
}
