<?php

declare(strict_types=1);

namespace Jellyfish\EventCache\EventErrorHandler;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Cache\CacheFacadeInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\EventCache\EventCacheConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;

class CacheEventErrorHandlerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Cache\CacheFacadeInterface
     */
    protected $cacheFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Event\EventInterface
     */
    protected $eventMock;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var string
     */
    protected $eventListenerIdentifier;

    /**
     * @var \Jellyfish\EventCache\EventErrorHandler\CacheEventErrorHandler
     */
    protected $cacheEventErrorHandler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheFacadeMock = $this->getMockBuilder(CacheFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exception = new Exception('Foo');

        $this->eventListenerIdentifier = 'foo';

        $this->cacheEventErrorHandler = new CacheEventErrorHandler(
            $this->cacheFacadeMock,
            $this->serializerFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        $json = '{}';
        $id = '97c2dcc3-bbcb-4890-bb50-a78f6bb748c9';

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        $this->serializerFacadeMock->expects(self::atLeastOnce())
            ->method('serialize')
            ->with($this->eventMock, 'json')
            ->willReturn($json);

        $this->cacheFacadeMock->expects(self::atLeastOnce())
            ->method('set')
            ->with($id, $json, EventCacheConstants::LIFE_TIME)
            ->willReturn($this->cacheFacadeMock);

        self::assertEquals(
            $this->cacheEventErrorHandler,
            $this->cacheEventErrorHandler->handle($this->exception, $this->eventListenerIdentifier, $this->eventMock)
        );
    }
}
