<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Jellyfish\Kernel\KernelInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;
use stdClass;
use Symfony\Component\Console\Command\Command;

class ConsoleTest extends Unit
{
    protected MockObject&KernelInterface $kernelMock;

    protected MockObject&Container $containerMock;

    protected Console $console;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernelMock = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernelMock->expects($this->atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->containerMock);

        $this->console = new Console($this->kernelMock);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testAllWithoutAdditionalDefaultCommands(): void
    {
        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetExists')
            ->with('commands')
            ->willReturn(false);

        $this->containerMock->expects($this->never())
            ->method('offsetGet')
            ->with('commands');

        $this->assertCount(4, $this->console->all());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testAllWithEmptyAdditionalDefaultCommands(): void
    {
        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetExists')
            ->with('commands')
            ->willReturn(true);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetGet')
            ->with('commands')
            ->willReturn(0);

        $this->assertCount(4, $this->console->all());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testAll(): void
    {
        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetExists')
            ->with('commands')
            ->willReturn(true);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetGet')
            ->with('commands')
            ->willReturn([new Command('foo:bar'), new stdClass()]);

        $this->assertCount(5, $this->console->all());
    }
}
