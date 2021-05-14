<?php

declare(strict_types=1);

namespace Jellyfish\EventLog\EventErrorHandler;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Event\EventInterface;
use Jellyfish\Log\LogFacadeInterface;
use Psr\Log\LoggerInterface;

class LogEventErrorHandlerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Log\LogFacadeInterface
     */
    protected $logFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Event\EventInterface
     */
    protected $eventMock;

    /**
     * @var \Exception
     */
    protected Exception $exception;

    /**
     * @var string
     */
    protected string $eventListenerIdentifier;

    /**
     * @var \Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler
     */
    protected LogEventErrorHandler $logEventErrorHandler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->logFacadeMock = $this->getMockBuilder(LogFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exception = new Exception('Foo');

        $this->eventListenerIdentifier = 'foo';

        $this->logEventErrorHandler = new LogEventErrorHandler($this->logFacadeMock);
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

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getId')
            ->willReturn($eventId);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getMetaProperties')
            ->willReturn($metaProperties);

        $this->logFacadeMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with($this->exception->getMessage(), $context);

        static::assertEquals(
            $this->logEventErrorHandler,
            $this->logEventErrorHandler->handle($this->exception, $this->eventListenerIdentifier, $this->eventMock)
        );
    }
}
