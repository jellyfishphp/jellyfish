<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Jellyfish\Event\EventQueueWorkerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventQueueWorkerStartCommandTest extends Unit
{
    protected MockObject&InputInterface $inputMock;

    protected MockObject&OutputInterface $outputMock;

    protected EventQueueWorkerInterface&MockObject $eventQueueWorkerMock;

    protected EventQueueWorkerStartCommand $eventQueueWorkerStartCommand;

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
