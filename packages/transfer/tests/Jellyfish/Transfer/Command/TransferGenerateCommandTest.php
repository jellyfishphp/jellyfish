<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Command;

use Codeception\Test\Unit;
use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Transfer\TransferFacadeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransferGenerateCommandTest extends Unit
{
    /**
     * @var \Jellyfish\Log\LogFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logFacadeMock;

    /**
     * @var \Jellyfish\Transfer\TransferFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferFacadeMock;

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

        $this->logFacadeMock = $this->getMockBuilder(LogFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferFacadeMock = $this->getMockBuilder(TransferFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferGenerateCommand = new TransferGenerateCommand(
            $this->transferFacadeMock,
            $this->logFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        static::assertEquals(TransferGenerateCommand::NAME, $this->transferGenerateCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        static::assertEquals(TransferGenerateCommand::DESCRIPTION, $this->transferGenerateCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->transferFacadeMock->expects(static::atLeastOnce())
            ->method('clean')
            ->willReturn($this->transferFacadeMock);

        $this->transferFacadeMock->expects(static::atLeastOnce())
            ->method('generate')
            ->willReturn($this->transferFacadeMock);

        $exitCode = $this->transferGenerateCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }
}
