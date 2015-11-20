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
     * @param $type
     * @param null $value
     * @return Token
     * @throws SyntaxException
     */
    public function expectCurrent($type, $value = null)
    {
        if ($this->current->test($type, $value)) {
            return $this->current;
        }
        $value   = $this->current->getValue();
        $message = "Unexpected {$this->current->getTypeString()}";
        if ($value === true) {
            $message .= ' (true)';
        } elseif ($value === false) {
            $message .= ' (false)';
        } elseif ($value !== '') {
            $message .= " ({$value})";
        }
        throw new SyntaxException($message);
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
}
