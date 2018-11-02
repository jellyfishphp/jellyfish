<?php

namespace Jellyfish\Queue\Command;

use Codeception\Test\Unit;
use Jellyfish\Queue\WorkerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartWorkerCommandTest extends Unit
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
     * @var \Jellyfish\Queue\WorkerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $workerMock;

    /**
     * @var \Jellyfish\Queue\Command\StartWorkerCommand
     */
    protected $startWorkerCommand;

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

        $this->workerMock = $this->getMockBuilder(WorkerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->startWorkerCommand = new StartWorkerCommand($this->workerMock);
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(StartWorkerCommand::NAME, $this->startWorkerCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(StartWorkerCommand::DESCRIPTION, $this->startWorkerCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->workerMock->expects($this->atLeastOnce())
            ->method('start');

        $exitCode = $this->startWorkerCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
