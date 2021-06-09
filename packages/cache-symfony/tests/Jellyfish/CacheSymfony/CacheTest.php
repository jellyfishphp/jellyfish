<?php

declare(strict_types=1);

namespace Jellyfish\CacheSymfony;

use Codeception\Test\Unit;
use Exception;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CacheTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Cache\Adapter\AbstractAdapter
     */
    protected $cacheAdapterMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Contracts\Cache\ItemInterface
     */
    protected $cacheItemMock;

    /**
     * @var \Jellyfish\CacheSymfony\CacheInterface
     */
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

        $this->cacheItemMock = $this->getMockBuilder(ItemInterface::class)
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

        $this->cacheAdapterMock->expects(static::atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(static::atLeastOnce())
            ->method('isHit')
            ->willReturn(true);

        $this->cacheItemMock->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn($value);

        static::assertEquals(
            $value,
            $this->cache->get($key)
        );
    }

    /**
     * @return void
     */
    public function testGetWithInvalidKey(): void
    {
        $key = 'key';

        $this->cacheAdapterMock->expects(static::atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(static::atLeastOnce())
            ->method('isHit')
            ->willReturn(false);

        static::assertEquals(
            null,
            $this->cache->get($key)
        );
    }

    /**
     * @return void
     */
    public function testSet(): void
    {
        $key = 'key';
        $value = '{}';
        $lifeTime = null;

        $this->cacheAdapterMock->expects(static::atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(static::atLeastOnce())
            ->method('set')
            ->with($value)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(static::atLeastOnce())
            ->method('expiresAfter')
            ->with($lifeTime)
            ->willReturn($this->cacheItemMock);

        $this->cacheAdapterMock->expects(static::atLeastOnce())
            ->method('save')
            ->with($this->cacheItemMock);

        static::assertEquals(
            $this->cache,
            $this->cache->set($key, $value, $lifeTime)
        );
    }

    /**
     * @return void
     */
    public function testSetWithInvalidLifeTime(): void
    {
        $key = 'key';
        $value = '{}';
        $lifeTime = 0;

        $this->cacheAdapterMock->expects(static::never())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(static::never())
            ->method('set')
            ->with($value)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(static::never())
            ->method('expiresAfter')
            ->with($lifeTime)
            ->willReturn($this->cacheItemMock);

        $this->cacheAdapterMock->expects(static::never())
            ->method('save')
            ->with($this->cacheItemMock);

        try {
            $this->cache->set($key, $value, $lifeTime);
            static::fail();
        } catch (Exception $exception) {
        }
    }
}
