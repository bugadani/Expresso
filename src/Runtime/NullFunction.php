<?php

namespace Expresso\Runtime;

class NullFunction extends RuntimeFunction
{
    public function __construct()
    {

    }

    public function __invoke(...$args)
    {

    }
}