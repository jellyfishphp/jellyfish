<?php

namespace Jellyfish\Scheduler\Command;

use Codeception\Test\Unit;
use Jellyfish\Scheduler\SchedulerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSchedulerCommandTest extends Unit
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
     * @var \Jellyfish\Scheduler\SchedulerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $schedulerMock;

    /**
     * @var \Jellyfish\Scheduler\Command\RunSchedulerCommand
     */
    protected $runSchedulerCommand;

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

        $this->schedulerMock = $this->getMockBuilder(SchedulerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->runSchedulerCommand = new RunSchedulerCommand($this->schedulerMock);
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(RunSchedulerCommand::NAME, $this->runSchedulerCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(RunSchedulerCommand::DESCRIPTION, $this->runSchedulerCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->schedulerMock->expects($this->atLeastOnce())
            ->method('run');

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
