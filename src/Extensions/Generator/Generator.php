<?php

namespace Expresso\Extensions\Generator;

use Expresso\Extension;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Lambda\Lambda;

class Generator extends Extension
{
    public function getDependencies()
    {
        return [
            Core::class,
            Lambda::class
        ];
    }
}