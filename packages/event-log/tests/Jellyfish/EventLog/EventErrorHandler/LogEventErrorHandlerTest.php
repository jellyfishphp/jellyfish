<?php

declare(strict_types = 1);

namespace Jellyfish\EventLog\EventErrorHandler;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Event\EventInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class LogEventErrorHandlerTest extends Unit
{
    protected MockObject&LoggerInterface $loggerMock;

    protected MockObject&EventInterface $eventMock;

    protected Exception $exception;

    protected string $eventListenerIdentifier;

    protected LogEventErrorHandler $logEventErrorHandler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exception = new Exception('Foo');

        $this->eventListenerIdentifier = 'foo';

        $this->logEventErrorHandler = new LogEventErrorHandler($this->loggerMock);
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        $metaProperties = [];
        $eventName = 'bar';
        $eventId = '97c2dcc3-bbcb-4890-bb50-a78f6bb748c9';
        $context = [
            'eventId' => $eventId,
            'eventListenerIdentifier' => $this->eventListenerIdentifier,
            'eventName' => $eventName,
            'eventMetaProperties' => $metaProperties,
            'trace' => $this->exception->getTrace(),
        ];

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn($eventId);

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getMetaProperties')
            ->willReturn($metaProperties);

        $this->loggerMock->expects(self::atLeastOnce())
            ->method('error')
            ->with($this->exception->getMessage(), $context);

        $this->assertEquals($this->logEventErrorHandler, $this->logEventErrorHandler->handle($this->exception, $this->eventListenerIdentifier, $this->eventMock));
    }
}
