<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Event\Exception\NotSupportedMethodException;
use Jellyfish\Event\Exception\NotSupportedTypeException;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractEventBulkListenerTest extends Unit
{
    protected MockObject&EventInterface $eventMock;

    protected EventErrorHandlerInterface&MockObject $errorHandlerMock;

    protected MockObject&AbstractEventBulkListener $abstractEventBulkListenerMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->errorHandlerMock = $this->getMockBuilder(EventErrorHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractEventBulkListenerMock = $this->getMockForAbstractClass(AbstractEventBulkListener::class);
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        try {
            $this->abstractEventBulkListenerMock->handle($this->eventMock);
            static::fail();
        } catch (NotSupportedMethodException) {
        }
    }

    /**
     * @return void
     */
    public function testHandleBulkWithNotSupportedType(): void
    {
        $events = [$this->eventMock];

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_SYNC);

        $this->abstractEventBulkListenerMock->expects($this->never())
            ->method('doHandle')
            ->with($this->eventMock);

        try {
            $this->abstractEventBulkListenerMock->handleBulk($events);
            $this->fail();
        } catch (NotSupportedTypeException) {
        }
    }

    /**
     * @return void
     */
    public function testHandleBulkWithUnhandledError(): void
    {
        $events = [$this->eventMock];
        $exception = new Exception();

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willThrowException($exception);

        try {
            $this->abstractEventBulkListenerMock->handleBulk($events);
            $this->fail();
        } catch (Exception) {
        }
    }

    /**
     * @return void
     */
    public function testHandleBulkWithHandledError(): void
    {
        $events = [$this->eventMock];
        $exception = new Exception();
        $identifier = 'foo';

        $this->abstractEventBulkListenerMock->addErrorHandler($this->errorHandlerMock);

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willThrowException($exception);

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn($identifier);

        $this->errorHandlerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->with($exception, $identifier, $this->eventMock)
            ->willReturn($this->errorHandlerMock);

        $this->assertEquals(
            $this->abstractEventBulkListenerMock,
            $this->abstractEventBulkListenerMock->handleBulk($events),
        );
    }

    /**
     * @return void
     */
    public function testHandleBulk(): void
    {
        $events = [$this->eventMock];

        $this->abstractEventBulkListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willReturn($this->abstractEventBulkListenerMock);

        $this->assertEquals(
            $this->abstractEventBulkListenerMock,
            $this->abstractEventBulkListenerMock->handleBulk($events),
        );
    }
}
