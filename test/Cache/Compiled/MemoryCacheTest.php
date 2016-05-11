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

        $this->cache->store('contained', 'content');

        $this->assertTrue($this->cache->contains('contained'));
        $this->assertEquals('content', $this->cache->retrieve('contained'));
    }

    public function testRetrieveException()
    {
        $this->expectException(\OutOfBoundsException::class);

        $this->cache->retrieve('missing');
    }
}