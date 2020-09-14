<?php

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
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $cacheItemMock;

    /**
     * @var \Jellyfish\Cache\CacheInterface
     */
    protected $cache;

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

        $this->cacheAdapterMock->expects(self::atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(self::atLeastOnce())
            ->method('isHit')
            ->willReturn(true);

        $this->cacheItemMock->expects(self::atLeastOnce())
            ->method('get')
            ->willReturn($value);

        self::assertEquals(
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

        $this->cacheAdapterMock->expects(self::atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(self::atLeastOnce())
            ->method('isHit')
            ->willReturn(false);

        self::assertEquals(
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

        $this->cacheAdapterMock->expects(self::atLeastOnce())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(self::atLeastOnce())
            ->method('set')
            ->with($value)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(self::atLeastOnce())
            ->method('expiresAfter')
            ->with($lifeTime)
            ->willReturn($this->cacheItemMock);

        $this->cacheAdapterMock->expects(self::atLeastOnce())
            ->method('save')
            ->with($this->cacheItemMock);

        self::assertEquals(
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

        $this->cacheAdapterMock->expects(self::never())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(self::never())
            ->method('set')
            ->with($value)
            ->willReturn($this->cacheItemMock);

        $this->cacheItemMock->expects(self::never())
            ->method('expiresAfter')
            ->with($lifeTime)
            ->willReturn($this->cacheItemMock);

        $this->cacheAdapterMock->expects(self::never())
            ->method('save')
            ->with($this->cacheItemMock);

        try {
            $this->cache->set($key, $value, $lifeTime);
            self::fail();
        } catch (Exception $exception) {
        }
    }
}
