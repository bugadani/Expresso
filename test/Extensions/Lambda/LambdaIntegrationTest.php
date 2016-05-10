<?php

namespace Expresso\Test\Extensions\Lambda;

use Expresso\Expresso;


use Expresso\Extensions\Lambda\Lambda;
use Expresso\Test\IntegrationTest;

class LambdaIntegrationTest extends IntegrationTest
{

    public function create()
    {
        $expresso = new Expresso();
        $expresso->addExtension(new Lambda());

        return $expresso;
    }

    public function getDirectory()
    {
        return __DIR__ . '/fixtures';
    }
}