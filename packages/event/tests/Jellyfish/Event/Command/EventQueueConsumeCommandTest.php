<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Exception;
use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Lock\LockFacadeInterface;
use Jellyfish\Lock\LockInterface;
use Jellyfish\Log\LogFacadeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventQueueConsumeCommandTest extends Unit
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inputMock;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $outputMock;

    /**
     * @var \Jellyfish\Event\EventFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFacadeMock;

    /**
     * @var \Jellyfish\Event\EventInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventBulkListenerMock;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

    /**
     * @var \Jellyfish\Lock\LockFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockFacadeMock;

    /**
     * @var \Jellyfish\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockMock;

    /**
     * @var \Jellyfish\Log\LogFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logFacadeMock;

    /**
     * @var \Jellyfish\Event\Command\EventQueueConsumeCommand
     */
    protected $eventQueueConsumeCommand;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var string
     */
    protected $listenerIdentifier;

    /**
     * @var array
     */
    protected $lockIdentifierParts;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventFacadeMock = $this->getMockBuilder(EventFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->eventListenerMock = $this->getMockBuilder(EventListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventBulkListenerMock = $this->getMockBuilder(EventBulkListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockFacadeMock = $this->getMockBuilder(LockFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockMock = $this->getMockBuilder(LockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logFacadeMock = $this->getMockBuilder(LogFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventName = 'test';
        $this->listenerIdentifier = 'testListener';
        $this->lockIdentifierParts = [EventQueueConsumeCommand::NAME, $this->eventName, $this->listenerIdentifier];

        $this->eventQueueConsumeCommand = new EventQueueConsumeCommand(
            $this->eventFacadeMock,
            $this->lockFacadeMock,
            $this->logFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        static::assertEquals(EventQueueConsumeCommand::NAME, $this->eventQueueConsumeCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        static::assertEquals(EventQueueConsumeCommand::DESCRIPTION, $this->eventQueueConsumeCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithLockedStatus(): void
    {
        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithEmptyQueue(): void
    {
        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('getEventListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('dequeueEvent')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithNoListener(): void
    {
        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('getEventListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

        $this->eventFacadeMock->expects(static::never())
            ->method('dequeueEvent');

        $this->eventFacadeMock->expects(static::never())
            ->method('dequeueEventBulk');

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('getEventListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('dequeueEvent')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventListenerMock->expects(static::atLeastOnce())
            ->method('handle')
            ->with($this->eventMock);

        $this->logFacadeMock->expects(static::never())
            ->method('error');

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithEventBulkListener(): void
    {
        $chunkSize = 100;
        $events = [$this->eventMock];

        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('getEventListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventBulkListenerMock);

        $this->eventBulkListenerMock->expects(static::atLeastOnce())
            ->method('getChunkSize')
            ->willReturn($chunkSize);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('dequeueEventBulk')
            ->with($this->eventName, $this->listenerIdentifier, $chunkSize)
            ->willReturn($events);

        $this->eventBulkListenerMock->expects(static::atLeastOnce())
            ->method('handleBulk')
            ->with($events)
            ->willReturn($this->eventBulkListenerMock);

        $this->logFacadeMock->expects(static::never())
            ->method('error');

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithHandlerException(): void
    {
        $exception = new Exception('Test exception');

        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('dequeueEvent')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('getEventListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventListenerMock->expects(static::atLeastOnce())
            ->method('handle')
            ->willThrowException($exception);

        $this->logFacadeMock->expects(static::atLeastOnce())
            ->method('error')
            ->with((string)$exception);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithInvalidArgument(): void
    {
        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls(null, $this->listenerIdentifier);

        $this->lockFacadeMock->expects(static::never())
            ->method('createLock');

        $this->lockMock->expects(static::never())
            ->method('acquire');

        $this->eventFacadeMock->expects(static::never())
            ->method('getEventListener');

        $this->eventFacadeMock->expects(static::never())
            ->method('dequeueEvent');

        $this->logFacadeMock->expects(static::never())
            ->method('error');

        $this->lockMock->expects(static::never())
            ->method('release');

        try {
            $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);
            static::fail();
        } catch (InvalidArgumentException $e) {
        }
    }
}
