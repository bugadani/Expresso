<?php

namespace Expresso\Extensions;

use Expresso\Extension;

class Generator extends Extension
{
    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\Core',
            __NAMESPACE__ . '\\Lambda'
        ];
    }
}