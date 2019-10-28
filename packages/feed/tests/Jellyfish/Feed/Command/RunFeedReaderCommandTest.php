<?php

declare(strict_types=1);

namespace Jellyfish\Feed\Command;

use Codeception\Test\Unit;
use InvalidArgumentException;
use Jellyfish\Feed\FeedReaderManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunFeedReaderCommandTest extends Unit
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
     * @var \Jellyfish\Feed\FeedReaderManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $feedReaderManagerMock;

    /**
     * @var \Jellyfish\Feed\Command\RunFeedReaderCommand
     */
    protected $runFeedReaderCommand;

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

        $this->feedReaderManagerMock = $this->getMockBuilder(FeedReaderManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->runFeedReaderCommand = new RunFeedReaderCommand($this->feedReaderManagerMock);
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(RunFeedReaderCommand::NAME, $this->runFeedReaderCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(RunFeedReaderCommand::DESCRIPTION, $this->runFeedReaderCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $identifier = 'test';

        $this->inputMock->expects($this->atLeastOnce())
            ->method('getArgument')
            ->with('identifier')
            ->willReturn($identifier);

        $this->feedReaderManagerMock->expects($this->atLeastOnce())
            ->method('readFromFeedReader')
            ->with($identifier);

        $exitCode = $this->runFeedReaderCommand->run($this->inputMock, $this->outputMock);

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
            ->with('identifier')
            ->willReturn(null);

        $this->feedReaderManagerMock->expects($this->never())
            ->method('readFromFeedReader');

        try {
            $this->runFeedReaderCommand->run($this->inputMock, $this->outputMock);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }
    }
}
