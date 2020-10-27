<?php

namespace Jellyfish\EventCache\EventErrorHandler;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Cache\CacheInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\EventCache\EventCacheConstants;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

use function sprintf;

class CacheEventErrorHandlerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Serializer\SerializerInterface
     */
    protected $serializerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Cache\CacheInterface
     */
    protected $cacheMock;

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

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        $this->serializerMock->expects(self::atLeastOnce())
            ->method('serialize')
            ->with($this->eventMock, 'json')
            ->willReturn($json);

        $this->cacheMock->expects(self::atLeastOnce())
            ->method('set')
            ->with($id, $json, EventCacheConstants::LIFE_TIME)
            ->willReturn($this->cacheMock);

        self::assertEquals(
            $this->cacheEventErrorHandler,
            $this->cacheEventErrorHandler->handle($this->exception, $this->eventListenerIdentifier, $this->eventMock)
        );
    }
}
