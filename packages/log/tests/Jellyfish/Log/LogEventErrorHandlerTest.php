<?php

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Event\EventInterface;
use Psr\Log\LoggerInterface;

class LogEventErrorHandlerTest extends Unit
{
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
    protected $exception;

    /**
     * @var string
     */
    protected $eventListenerIdentifier;

    /**
     * @var \FondOfJellyfish\Log\LogEventErrorHandler
     */
    protected $logEventErrorHandler;

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
        $context = [
            'eventListenerIdentifier' => $this->eventListenerIdentifier,
            'eventName' => $eventName,
            'eventMetaProperties' => $metaProperties,
            'trace' => $this->exception->getTrace(),
        ];

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getMetaProperties')
            ->willReturn($metaProperties);

        $this->loggerMock->expects(self::atLeastOnce())
            ->method('error')
            ->with($this->exception->getMessage(), $context);

        self::assertEquals(
            $this->logEventErrorHandler,
            $this->logEventErrorHandler->handle($this->exception, $this->eventListenerIdentifier, $this->eventMock)
        );
    }
}
