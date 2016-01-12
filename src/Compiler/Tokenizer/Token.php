<?php

namespace Expresso\Compiler\Tokenizer;

class Token
{
    const CONSTANT = 0;
    const STRING = 1;
    const IDENTIFIER = 2;
    const OPERATOR = 3;
    const PUNCTUATION = 4;
    const EOF = 5;

    private static $strings = [
        self::CONSTANT    => 'LITERAL',
        self::STRING      => 'STRING',
        self::IDENTIFIER  => 'IDENTIFIER',
        self::OPERATOR    => 'OPERATOR',
        self::PUNCTUATION => 'PUNCTUATION',
        self::EOF         => 'EOF',
    ];

    private $type;
    private $value;
    private $line;
    private $offset;

    public function __construct($type, $value = null)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    public function test($type, $value = null)
    {
        if ($this->type !== $type) {
            return false;
        }
        if ($value === null || $this->value === $value) {
            return true;
        }
        if (is_array($value) && in_array($this->value, $value, true)) {
            return true;
        }
        if (is_callable($value)) {
            return $value($this->value);
        }

        return false;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getTypeString()
    {
        if (isset(self::$strings[ $this->type ])) {
            return self::$strings[ $this->type ];
        }

        return "UNKNOWN {$this->type}";
    }

    /**
     * @param int $line
     * @param int $offset
     */
    public function setPosition($line, $offset)
    {
        $this->line   = $line;
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    public function __toString()
    {
        $value = $this->value;
        if ($value === true) {
            $valueString = ' (true)';
        } else if ($value === false) {
            $valueString = ' (false)';
        } else if ($value === null) {
            $valueString = ' (null)';
        } else if(is_callable($value)) {
            $valueString = " (callback)";
        } else {
            $valueString = " ({$value})";
        }

        return $this->getTypeString() . $valueString;
    }
}
