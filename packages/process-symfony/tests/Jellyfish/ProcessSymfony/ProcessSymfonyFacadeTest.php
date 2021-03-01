<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;

class ProcessSymfonyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\ProcessSymfony\ProcessSymfonyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processSymfonyFactoryMock;

    /**
     * @var \Jellyfish\ProcessSymfony\Process|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processMock;

    /**
     * @var \Jellyfish\ProcessSymfony\ProcessSymfonyFacade
     */
    protected $processSymfonyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->processSymfonyFactoryMock = $this->getMockBuilder(ProcessSymfonyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processMock = $this->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processSymfonyFacade = new ProcessSymfonyFacade($this->processSymfonyFactoryMock);
    }

    public function testCreateProcess(): void
    {
        $command = ['ls', '-la'];

        $this->processSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with($command)
            ->willReturn($this->processMock);

        static::assertEquals($this->processMock, $this->processSymfonyFacade->createProcess($command));
    }
}
