<?php

declare(strict_types = 1);

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Jellyfish\Kernel\KernelInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use League\Route\Router;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpTest extends Unit
{
    protected MockObject&KernelInterface $kernelMock;

    protected MockObject&Container $containerMock;

    protected ServerRequestInterface&MockObject $requestMock;

    protected ResponseInterface&MockObject $responseMock;

    protected Router&MockObject $routerMock;

    protected MockObject&EmitterInterface $emitterMock;

    protected Http $http;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->requestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->emitterMock = $this->getMockBuilder(EmitterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernelMock = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->http = new Http($this->kernelMock);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->kernelMock->expects($this->atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->containerMock);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetGet')
            ->willReturnCallback(
                fn (string $index) => match($index) {
                    'request' => $this->requestMock,
                    'router' => $this->routerMock,
                    'emitter' => $this->emitterMock,
                    default => throw new LogicException('Unsupported parameter.')
                },
            );

        $this->routerMock->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->requestMock)
            ->willReturn($this->responseMock);

        $this->emitterMock->expects($this->atLeastOnce())
            ->method('emit')
            ->with($this->responseMock);


        $this->http->run();
    }
}
