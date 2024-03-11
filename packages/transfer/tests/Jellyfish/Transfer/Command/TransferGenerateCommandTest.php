<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Command;

use Codeception\Test\Unit;
use Jellyfish\Transfer\TransferCleanerInterface;
use Jellyfish\Transfer\TransferGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransferGenerateCommandTest extends Unit
{
    protected MockObject&LoggerInterface $loggerMock;

    protected TransferGeneratorInterface&MockObject $transferGeneratorMock;

    protected TransferCleanerInterface&MockObject $transferCleanerMock;

    protected MockObject&InputInterface $inputMock;

    protected MockObject&OutputInterface $outputMock;

    protected TransferGenerateCommand $transferGenerateCommand;

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
        $this->assertSame(TransferGenerateCommand::NAME, $this->transferGenerateCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertSame(TransferGenerateCommand::DESCRIPTION, $this->transferGenerateCommand->getDescription());
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

        $this->assertSame(0, $exitCode);
    }
}
