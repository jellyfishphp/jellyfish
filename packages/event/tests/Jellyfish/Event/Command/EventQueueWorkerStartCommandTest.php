<?php

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Jellyfish\Event\EventQueueWorkerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventQueueWorkerStartCommandTest extends Unit
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
     * @var \Jellyfish\Event\EventQueueWorkerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueWorkerMock;

    /**
     * @var \Jellyfish\Event\Command\EventQueueWorkerStartCommand
     */
    protected $eventQueueWorkerStartCommand;

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

        $this->eventQueueWorkerMock = $this->getMockBuilder(EventQueueWorkerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueWorkerStartCommand = new EventQueueWorkerStartCommand($this->eventQueueWorkerMock);
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(EventQueueWorkerStartCommand::NAME, $this->eventQueueWorkerStartCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(
            EventQueueWorkerStartCommand::DESCRIPTION,
            $this->eventQueueWorkerStartCommand->getDescription()
        );
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->eventQueueWorkerMock->expects($this->atLeastOnce())
            ->method('start');
        $exitCode = $this->eventQueueWorkerStartCommand->run($this->inputMock, $this->outputMock);
        $this->assertEquals(0, $exitCode);
    }
}
