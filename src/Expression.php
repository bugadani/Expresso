<?php

namespace Expresso;

class Expression
{
    /**
     * @var callable
     */
    private $callback;
    private $source;

    public function __construct(callable $callback, $source)
    {
        $this->callback = $callback;
        $this->source   = $source;
    }

    public function __toString()
    {
        return $this->source;
    }

    public function __invoke(array $parameters)
    {
        $callback = $this->callback;

        return $callback($parameters);
    }
}