<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Event\EventListenerProviderInterface;
use Jellyfish\Event\EventQueueConsumerInterface;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockInterface;
use Psr\Log\LoggerInterface;
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
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

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

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventName = 'test';
        $this->listenerIdentifier = 'testListener';
        $this->lockIdentifierParts = [EventQueueConsumeCommand::NAME, $this->eventName, $this->listenerIdentifier];

        $this->eventQueueConsumeCommand = new EventQueueConsumeCommand(
            $this->eventListenerProviderMock,
            $this->eventQueueConsumerMock,
            $this->lockFactoryMock,
            $this->loggerMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(EventQueueConsumeCommand::NAME, $this->eventQueueConsumeCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(EventQueueConsumeCommand::DESCRIPTION, $this->eventQueueConsumeCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithLockedStatus(): void
    {
        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithEmptyQueue(): void
    {
        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeue')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithNoListener(): void
    {
        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

        $this->eventQueueConsumerMock->expects($this->never())
            ->method('dequeue');

        $this->eventQueueConsumerMock->expects($this->never())
            ->method('dequeueBulk');

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeue')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->with($this->eventMock);

        $this->loggerMock->expects($this->never())
            ->method('error');

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
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

        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventBulkListenerMock);

        $this->eventBulkListenerMock->expects($this->atLeastOnce())
            ->method('getChunkSize')
            ->willReturn($chunkSize);

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeueBulk')
            ->with($this->eventName, $this->listenerIdentifier, $chunkSize)
            ->willReturn($events);

        $this->eventBulkListenerMock->expects($this->atLeastOnce())
            ->method('handleBulk')
            ->with($events)
            ->willReturn($this->eventBulkListenerMock);

        $this->loggerMock->expects($this->never())
            ->method('error');

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithHandlerException(): void
    {
        $exception = new \Exception('Test exception');

        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeue')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->willThrowException($exception);

        $this->loggerMock->expects($this->atLeastOnce())
            ->method('error')
            ->with((string)$exception);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithInvalidArgument(): void
    {
        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls(null, $this->listenerIdentifier);

        $this->lockFactoryMock->expects($this->never())
            ->method('create');

        $this->lockMock->expects($this->never())
            ->method('acquire');

        $this->eventListenerProviderMock->expects($this->never())
            ->method('getListener');

        $this->eventQueueConsumerMock->expects($this->never())
            ->method('dequeue');

        $this->loggerMock->expects($this->never())
            ->method('error');

        $this->lockMock->expects($this->never())
            ->method('release');

        try {
            $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }
    }
}
