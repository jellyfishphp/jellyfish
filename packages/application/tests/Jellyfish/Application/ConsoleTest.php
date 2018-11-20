<?php

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Jellyfish\Kernel\KernelInterface;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;

class ConsoleTest extends Unit
{
    /**
     * @var \Jellyfish\Application\Console
     */
    protected $console;

    /**
     * @var \Jellyfish\Kernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $kernelMock;

    /**
     * @var \Pimple\Container|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerMock;

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

        $this->assertCount(2, $this->console->all());
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

        $this->assertCount(2, $this->console->all());
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
            ->willReturn([new Command('foo:bar'), new \stdClass()]);

        $this->assertCount(3, $this->console->all());
    }
}
