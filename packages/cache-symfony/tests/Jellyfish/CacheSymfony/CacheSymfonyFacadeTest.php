<?php

declare(strict_types=1);

namespace Jellyfish\CacheSymfony;

use Codeception\Test\Unit;

class CacheSymfonyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\CacheSymfony\CacheSymfonyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cacheSymfonyFactoryMock;

    /**
     * @var \Jellyfish\CacheSymfony\CacheInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cacheMock;

    /**
     * @var \Jellyfish\CacheSymfony\CacheSymfonyFacade
     */
    protected $cacheSymfonyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cacheSymfonyFactoryMock = $this->getMockBuilder(CacheSymfonyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheSymfonyFacade = new CacheSymfonyFacade($this->cacheSymfonyFactoryMock);
    }

    /**
     * @return void
     */
    public function testGet(): void
    {
        $key = 'foo';
        $value = 'bar';

        $this->cacheSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getCache')
            ->willReturn($this->cacheMock);

        $this->cacheMock->expects(static::atLeastOnce())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        static::assertEquals($value, $this->cacheSymfonyFacade->get($key));
    }

    /**
     * @throws \Jellyfish\Cache\Exception\InvalidLifeTimeException
     */
    public function testSet(): void {
        $key = 'foo';
        $value = 'bar';

        $this->cacheSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getCache')
            ->willReturn($this->cacheMock);

        $this->cacheMock->expects(static::atLeastOnce())
            ->method('set')
            ->with($key, $value, null)
            ->willReturn($this->cacheMock);

        static::assertEquals($this->cacheSymfonyFacade, $this->cacheSymfonyFacade->set($key, $value));
    }
}
