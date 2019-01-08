<?php

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Jellyfish\Event\EventDispatcherInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Event\EventQueueConsumerInterface;
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
     * @var \Jellyfish\Event\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventDispatcherMock;

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
    protected $eventListenerMock;

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

        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
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

        $this->eventName = 'test';
        $this->listenerIdentifier = 'testListener';

        $this->eventQueueConsumeCommand = new EventQueueConsumeCommand(
            $this->eventDispatcherMock,
            $this->eventQueueConsumerMock
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
    public function testRunWithEmptyQueue(): void
    {
        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->withConsecutive(['eventName'], ['listenerIdentifier'])
            ->willReturnOnConsecutiveCalls($this->eventName, $this->listenerIdentifier);

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeueEvent')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

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

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeueEvent')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventDispatcherMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn(null);

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

        $this->eventQueueConsumerMock->expects($this->atLeastOnce())
            ->method('dequeueEvent')
            ->with($this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventMock);

        $this->eventDispatcherMock->expects($this->atLeastOnce())
            ->method('getListener')
            ->with(EventListenerInterface::TYPE_ASYNC, $this->eventName, $this->listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->with($this->eventMock);

        $exitCode = $this->eventQueueConsumeCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
