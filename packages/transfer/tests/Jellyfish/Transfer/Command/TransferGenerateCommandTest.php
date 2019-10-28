<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Command;

use Codeception\Test\Unit;
use Jellyfish\Transfer\TransferCleanerInterface;
use Jellyfish\Transfer\TransferGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransferGenerateCommandTest extends Unit
{
    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    /**
     * @var \Jellyfish\Transfer\TransferGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferGeneratorMock;

    /**
     * @var \Jellyfish\Transfer\TransferCleanerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferCleanerMock;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inputMock;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $outputMock;

    /**
     * @var \Jellyfish\Transfer\Command\TransferGenerateCommand
     */
    protected $transferGenerateCommand;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferGeneratorMock = $this->getMockBuilder(TransferGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferCleanerMock = $this->getMockBuilder(TransferCleanerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferGenerateCommand = new TransferGenerateCommand(
            $this->transferGeneratorMock,
            $this->transferCleanerMock,
            $this->loggerMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(TransferGenerateCommand::NAME, $this->transferGenerateCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(TransferGenerateCommand::DESCRIPTION, $this->transferGenerateCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->transferCleanerMock->expects($this->atLeastOnce())
            ->method('clean')
            ->willReturn($this->transferCleanerMock);

        $this->transferGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->willReturn($this->transferGeneratorMock);

        $exitCode = $this->transferGenerateCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
