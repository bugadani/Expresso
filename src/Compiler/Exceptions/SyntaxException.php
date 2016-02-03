<?php

namespace Expresso\Compiler\Exceptions;

class SyntaxException extends ExpressionException
{
    private $lineNumber;
    private $offset;

    public function __construct($message, $line, $offset, $prev = null)
    {
        parent::__construct("{$message} in line {$line} at position {$offset}", 0, $prev);
        $this->lineNumber = $line;
        $this->offset     = $offset;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @return \Exception
     */
    public function getOffset()
    {
        return $this->offset;
    }
}
