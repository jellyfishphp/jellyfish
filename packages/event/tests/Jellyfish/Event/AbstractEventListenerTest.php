<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Exception;

class AbstractEventListenerTest extends Unit
{
    /**
     * @var \Jellyfish\Event\AbstractEventListener|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $abstractEventListenerMock;

    /**
     * @var \Jellyfish\Event\EventInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Event\EventErrorHandlerInterface
     */
    protected $errorHandlerMock;

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->errorHandlerMock = $this->getMockBuilder(EventErrorHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractEventListenerMock = $this->getMockForAbstractClass(AbstractEventListener::class);
    }

    /**
     * @return void
     */
    public function testSetAndGetErrorHandlers(): void
    {
        $errorHandlers = [$this->errorHandlerMock];

        $this->assertEquals(
            $this->abstractEventListenerMock,
            $this->abstractEventListenerMock->setErrorHandlers($errorHandlers)
        );

        $this->assertEquals(
            $errorHandlers,
            $this->abstractEventListenerMock->getErrorHandlers()
        );
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testHandle(): void
    {
        $this->abstractEventListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willReturn($this->abstractEventListenerMock);

        $this->assertEquals(
            $this->abstractEventListenerMock,
            $this->abstractEventListenerMock->handle($this->eventMock)
        );
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testHandleWithUnhandledError(): void
    {
        $this->abstractEventListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willThrowException(new Exception('Lorem ipsum'));

        try {
            $this->abstractEventListenerMock->handle($this->eventMock);
            $this->fail();
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testHandleWithHandledError(): void
    {
        $identifier = 'foo';
        $exception = new Exception();

        $this->abstractEventListenerMock->addErrorHandler($this->errorHandlerMock);

        $this->abstractEventListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willThrowException($exception);

        $this->abstractEventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn($identifier);

        $this->errorHandlerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->with($exception, $identifier, $this->eventMock)
            ->willReturn($this->errorHandlerMock);

        $this->assertEquals(
            $this->abstractEventListenerMock,
            $this->abstractEventListenerMock->handle($this->eventMock)
        );
    }
}
