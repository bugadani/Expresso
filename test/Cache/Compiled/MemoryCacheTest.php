<?php

namespace Expresso\Test\Cache\Compiled;

use Expresso\Cache\Compiled\MemoryCache;

class MemoryCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MemoryCache
     */
    private $cache;

    protected function setUp()
    {
        $this->cache = new MemoryCache();
    }

    public function testCache()
    {
        $this->assertFalse($this->cache->contains('missing'));
        $this->assertFalse($this->cache->contains('contained'));

        $this->cache->store('contained', 'function(){return 2;}');

        $this->assertTrue($this->cache->contains('contained'));
        $retrieved = $this->cache->retrieve('contained');

        $this->assertEquals(2, $retrieved());
    }

    public function testRetrieveException()
    {
        $this->expectException(\OutOfBoundsException::class);

        $this->cache->retrieve('missing');
    }
}