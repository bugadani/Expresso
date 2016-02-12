<?php

namespace Expresso\Test\Extensions\Core;

use Expresso\Expresso;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Generator\Generator;
use Expresso\Extensions\Lambda\Lambda;
use Expresso\Test\IntegrationTest;

class CoreIntegrationTest extends IntegrationTest
{

    public function create()
    {
        $expresso = new Expresso();
        $expresso->addExtension(new Core());
        $expresso->addExtension(new Lambda());
        $expresso->addExtension(new Generator());

        return $expresso;
    }

    public function getDirectory()
    {
        return __DIR__ . '/fixtures';
    }
}