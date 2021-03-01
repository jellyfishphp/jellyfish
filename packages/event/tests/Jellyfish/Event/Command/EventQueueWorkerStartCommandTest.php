<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Jellyfish\Event\EventFacadeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventQueueWorkerStartCommandTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFacadeMock;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inputMock;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $outputMock;

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

        $this->eventFacadeMock = $this->getMockBuilder(EventFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueWorkerStartCommand = new EventQueueWorkerStartCommand($this->eventFacadeMock);
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        static::assertEquals(EventQueueWorkerStartCommand::NAME, $this->eventQueueWorkerStartCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        static::assertEquals(
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
        $this->eventFacadeMock->expects(self::atLeastOnce())
            ->method('startEventQueueWorker');

        $exitCode = $this->eventQueueWorkerStartCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }
}
