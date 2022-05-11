<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Kernel\KernelInterface;
use Pimple\Container;
use stdClass;
use Symfony\Component\Console\Command\Command;

class ConsoleTest extends Unit
{
    /**
     * @var \Jellyfish\Kernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $kernelMock;

    /**
     * @var \Pimple\Container|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerMock;

    /**
     * @var \Jellyfish\Console\ConsoleFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $consoleFacadeMock;

    /**
     * @var \Jellyfish\Application\Console
     */
    protected Console $console;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->kernelMock = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consoleFacadeMock = $this->getMockBuilder(ConsoleFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->console = new Console($this->kernelMock);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testAllWithoutAdditionalDefaultCommands(): void
    {
        $this->kernelMock->expects(static::atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->containerMock);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('offsetGet')
            ->with(ConsoleConstants::FACADE)
            ->willReturn($this->consoleFacadeMock);

        $this->consoleFacadeMock->expects(static::atLeastOnce())
            ->method('getCommands')
            ->willReturn([]);

        static::assertGreaterThan(1, $this->console->all());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testAll(): void
    {
        $this->kernelMock->expects(static::atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->containerMock);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('offsetGet')
            ->with(ConsoleConstants::FACADE)
            ->willReturn($this->consoleFacadeMock);

        $command = new Command('foo:bar');

        $this->consoleFacadeMock->expects(static::atLeastOnce())
            ->method('getCommands')
            ->willReturn([$command, new stdClass()]);

        $commands = $this->console->all();
        self::assertContains($command, $commands);
    }
}
