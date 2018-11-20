<?php

namespace Jellyfish\Queue\Command;

use Codeception\Test\Unit;
use Jellyfish\Queue\JobManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunJobCommandTest extends Unit
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
     * @var \Jellyfish\Queue\JobManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobManagerMock;

    /**
     * @var \Jellyfish\Queue\Command\RunJobCommand
     */
    protected $runJobCommand;

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

        $this->jobManagerMock = $this->getMockBuilder(JobManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->runJobCommand = new RunJobCommand($this->jobManagerMock);
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(RunJobCommand::NAME, $this->runJobCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(RunJobCommand::DESCRIPTION, $this->runJobCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $queueName = 'test';

        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->with('queue')
            ->willReturn($queueName);

        $this->jobManagerMock->expects($this->atLeastOnce())
            ->method('runJob')
            ->with($queueName);

        $exitCode = $this->runJobCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
