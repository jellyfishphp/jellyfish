<?php

namespace Jellyfish\EventCache\EventErrorHandler;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Cache\CacheInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\EventCache\EventCacheConstants;
use Jellyfish\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class CacheEventErrorHandlerTest extends Unit
{
    protected SerializerInterface&MockObject $serializerMock;

    protected CacheInterface&MockObject $cacheMock;

    protected MockObject&EventInterface $eventMock;

    protected Exception $exception;

    protected string $eventListenerIdentifier;

    protected CacheEventErrorHandler $cacheEventErrorHandler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exception = new Exception('Foo');

        $this->eventListenerIdentifier = 'foo';

        $this->cacheEventErrorHandler = new CacheEventErrorHandler(
            $this->cacheMock,
            $this->serializerMock
        );
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        $json = '{}';
        $id = '97c2dcc3-bbcb-4890-bb50-a78f6bb748c9';

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($this->eventMock, 'json')
            ->willReturn($json);

        $this->cacheMock->expects($this->atLeastOnce())
            ->method('set')
            ->with($id, $json, EventCacheConstants::LIFE_TIME)
            ->willReturn($this->cacheMock);

        $this->assertEquals($this->cacheEventErrorHandler, $this->cacheEventErrorHandler->handle($this->exception, $this->eventListenerIdentifier, $this->eventMock));
    }
}
