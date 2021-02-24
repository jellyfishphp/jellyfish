<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Exception;
use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Event\EventListenerProviderInterface;
use Jellyfish\Event\EventQueueConsumerInterface;
use Jellyfish\Lock\LockFactoryInterface;
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
     * @var \Jellyfish\Event\EventListenerProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerProviderMock;

    /**
     * @var \Jellyfish\Event\EventQueueConsumerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueConsumerMock;

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
     * @var \Jellyfish\Lock\LockFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockFactoryMock;

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

        $this->eventListenerProviderMock = $this->getMockBuilder(EventListenerProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueConsumerMock = $this->getMockBuilder(EventQueueConsumerInterface::class)
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

        $this->lockFactoryMock = $this->getMockBuilder(LockFactoryInterface::class)
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
            $this->eventListenerProviderMock,
            $this->eventQueueConsumerMock,
            $this->lockFactoryMock,
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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventQueueConsumerMock->expects(static::atLeastOnce())
            ->method('dequeue')
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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

        $this->eventQueueConsumerMock->expects(static::never())
            ->method('dequeue');

        $this->eventQueueConsumerMock->expects(static::never())
            ->method('dequeueBulk');

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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventQueueConsumerMock->expects(static::atLeastOnce())
            ->method('dequeue')
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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventBulkListenerMock);

        $this->eventBulkListenerMock->expects(static::atLeastOnce())
            ->method('getChunkSize')
            ->willReturn($chunkSize);

        $this->eventQueueConsumerMock->expects(static::atLeastOnce())
            ->method('dequeueBulk')
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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventQueueConsumerMock->expects(static::atLeastOnce())
            ->method('dequeue')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('getListener')
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

        $this->lockFactoryMock->expects(static::never())
            ->method('create');

        $this->lockMock->expects(static::never())
            ->method('acquire');

        $this->eventListenerProviderMock->expects(static::never())
            ->method('getListener');

        $this->eventQueueConsumerMock->expects(static::never())
            ->method('dequeue');

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
