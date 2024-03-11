<?php

namespace Jellyfish\CacheSymfony;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Cache\CacheInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\ItemInterface;

class CacheTest extends Unit
{
    protected AbstractAdapter&MockObject $cacheAdapterMock;

    protected ItemInterface&MockObject $cacheItemMock;

    protected CacheInterface $cache;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cacheAdapterMock = $this->getMockBuilder(AbstractAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @phpstan-ignore-next-line */
        $this->cacheItemMock = $this->getMockBuilder(CacheItem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache = new Cache($this->cacheAdapterMock);
    }

    /**
     * @return void
     */
    public function testGet(): void
    {
        $key = 'key';
        $value = '{}';

        $this->cacheAdapterMock->expects($this->atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects($this->atLeastOnce())
            ->method('isHit')
            ->willReturn(true);

        $this->cacheItemMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($value);

        $this->assertSame($value, $this->cache->get($key));
    }

    /**
     * @return void
     */
    public function testGetWithInvalidKey(): void
    {
        $key = 'key';

        $this->cacheAdapterMock->expects($this->atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects($this->atLeastOnce())
            ->method('isHit')
            ->willReturn(false);

        $this->assertEquals(null, $this->cache->get($key));
    }

    /**
     * @return void
     */
    public function testSet(): void
    {
        $key = 'key';
        $value = '{}';
        $lifeTime = null;

        $this->cacheAdapterMock->expects($this->atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects($this->atLeastOnce())
            ->method('set')
            ->with($value)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects($this->atLeastOnce())
            ->method('expiresAfter')
            ->with($lifeTime)
            ->willReturn($this->cacheItemMock);

        $this->cacheAdapterMock->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->cacheItemMock);

        $this->assertEquals($this->cache, $this->cache->set($key, $value, $lifeTime));
    }

    /**
     * @return void
     */
    public function testSetWithInvalidLifeTime(): void
    {
        $key = 'key';
        $value = '{}';
        $lifeTime = 0;

        $this->cacheAdapterMock->expects($this->never())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects($this->never())
            ->method('set')
            ->with($value)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects($this->never())
            ->method('expiresAfter')
            ->with($lifeTime)
            ->willReturn($this->cacheItemMock);

        $this->cacheAdapterMock->expects($this->never())
            ->method('save')
            ->with($this->cacheItemMock);

        try {
            $this->cache->set($key, $value, $lifeTime);
            static::fail();
        } catch (Exception) {
        }
    }
}
