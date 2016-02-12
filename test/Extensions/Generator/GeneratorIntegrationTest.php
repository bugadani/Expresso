<?php

namespace Expresso\Test\Extensions\Generator;

use Expresso\Expresso;
use Expresso\Extensions\Generator\Generator;
use Expresso\Test\IntegrationTest;

class GeneratorIntegrationTest extends IntegrationTest
{

    public function create()
    {
        $expresso = new Expresso();
        $expresso->addExtension(new Generator());

        return $expresso;
    }

    public function getDirectory()
    {
        return __DIR__ . '/fixtures';
    }
}