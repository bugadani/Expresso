<?php

namespace Expresso\Utils;

class TransformIterator extends \IteratorIterator
{
    /**
     * @var \Closure
     */
    private $transformation;

    public function __construct(\Iterator $source, callable $transform = null)
    {
        $this->transformation = $transform;

        parent::__construct($source);
    }

    public function current()
    {
        return call_user_func($this->transformation, parent::current());
    }
}