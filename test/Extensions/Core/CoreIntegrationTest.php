<?php

namespace Expresso\Test\Extensions\Core;

use Expresso\Expresso;
use Expresso\Extensions\Core\Core;
use Expresso\Test\IntegrationTest;

class CoreIntegrationTest extends IntegrationTest
{

    public function create()
    {
        $expresso = new Expresso();
        $expresso->addExtension(new Core());

        return $expresso;
    }

    public function getDirectory()
    {
        return __DIR__ . '/fixtures';
    }
}