<?php

namespace Expresso\Compiler;

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

    /**
     * @var Token
     */
    private $next;

    public function __construct(\Iterator $tokens)
    {
        $this->tokens = $tokens;
        $tokens->rewind();
        $this->current = $tokens->current();
        $tokens->next();
        $this->next = $tokens->current();
    }

    /**
     * @return Token
     */
    public function next()
    {
        $this->current = $this->next;
        $this->tokens->next();
        $this->next = $this->tokens->current();

        return $this->current;
    }

    /**
     * @return Token
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return Token
     */
    public function expect($type, $value = null)
    {
        $this->next();

        return $this->expectCurrent($type, $value);
    }

    /**
     * @param      $type
     * @param null $value
     *
     * @return Token
     * @throws SyntaxException
     */
    public function expectCurrent($type, $value = null)
    {
        if ($this->current->test($type, $value)) {
            return $this->current;
        }
        $expectation = new Token($type, $value);
        throw new SyntaxException("Unexpected {$this->current}, expected {$expectation}");
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool|Token
     */
    public function nextTokenIf($type, $value = null)
    {
        if ($this->next->test($type, $value)) {
            return $this->next();
        }

        return false;
    }

    public function consume()
    {
        $current = $this->current;
        $this->next();

        return $current;
    }
}
