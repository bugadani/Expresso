<?php

namespace Expresso\Test\Cache\Parsed;

use Expresso\Cache\Parsed\MemoryCache;
use Expresso\Extensions\Core\Nodes\DataNode;

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

        $node = new DataNode('value');
        $this->cache->store('contained', $node);

        $this->assertTrue($this->cache->contains('contained'));
        $this->assertEquals($node, $this->cache->retrieve('contained'));
    }

    public function testRetrieveException()
    {
        $this->expectException(\OutOfBoundsException::class);

        $this->cache->retrieve('missing');
    }
}