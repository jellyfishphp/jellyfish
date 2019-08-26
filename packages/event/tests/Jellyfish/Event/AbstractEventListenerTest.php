<?php

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
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractEventListenerMock = $this->getMockForAbstractClass(AbstractEventListener::class);
    }

    /**
     * @return void
     */
    public function testSetErrorHandler(): void
    {
        $this->assertEquals(
            $this->abstractEventListenerMock,
            $this->abstractEventListenerMock->setErrorHandler(function (Exception $exception, EventInterface $event) {
            })
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
        $eventName = 'Test';
        $exceptionMessage = 'Lorem ipsum';
        $errorMessage = '';

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->assertEquals(
            $this->abstractEventListenerMock,
            $this->abstractEventListenerMock->setErrorHandler(
                function (Exception $exception, EventInterface $event) use (&$errorMessage) {
                    $errorMessage = \sprintf(
                        'EventName: %s / Exception: %s',
                        $event->getName(),
                        $exception->getMessage()
                    );
                }
            )
        );

        $this->abstractEventListenerMock->expects($this->atLeastOnce())
            ->method('doHandle')
            ->with($this->eventMock)
            ->willThrowException(new Exception($exceptionMessage));

        $this->assertEquals(
            $this->abstractEventListenerMock,
            $this->abstractEventListenerMock->handle($this->eventMock)
        );

        $this->assertEquals(
            $errorMessage,
            \sprintf('EventName: %s / Exception: %s', $eventName, $exceptionMessage)
        );
    }
}
