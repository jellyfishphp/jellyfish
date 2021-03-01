<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use ArrayObject;
use Codeception\Test\Unit;
use Symfony\Component\Console\Command\Command;

class ConsoleFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\Console\ConsoleFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $consoleFactoryMock;

    /**
     * @var \ArrayObject|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $commandListMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Console\Command\Command
     */
    protected $commandMock;

    /**
     * @var \Jellyfish\Console\ConsoleFacade
     */
    protected $consoleFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->consoleFactoryMock = $this->getMockBuilder(ConsoleFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandListMock = $this->getMockBuilder(ArrayObject::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandMock = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consoleFacade = new ConsoleFacade($this->consoleFactoryMock);
    }

    /**
     * @return void
     */
    public function testAddCommand(): void
    {
        $this->consoleFactoryMock->expects(static::atLeastOnce())
            ->method('getCommandList')
            ->willReturn($this->commandListMock);

        $this->commandListMock->expects(static::atLeastOnce())
            ->method('append')
            ->with($this->commandMock);

        static::assertEquals(
            $this->consoleFacade,
            $this->consoleFacade->addCommand($this->commandMock)
        );
    }

    /**
     * @return void
     */
    public function testGetCommands(): void
    {
        $this->consoleFactoryMock->expects(static::atLeastOnce())
            ->method('getCommandList')
            ->willReturn($this->commandListMock);

        $this->commandListMock->expects(static::atLeastOnce())
            ->method('getArrayCopy')
            ->willReturn([$this->commandMock]);

        $commands = $this->consoleFacade->getCommands();

        static::assertCount(1, $commands);
        static::assertEquals($this->commandMock, $commands[0]);
    }
}
